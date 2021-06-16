<?php

namespace App\Command;

use App\Entity\Offer;
use App\Entity\Product;
use App\Entity\ProductGroup;
use App\Entity\Site;
use App\Service\ProductDeleteExcess;
use App\Service\Retail\RetailAPI;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
 * Консольная команда для получения списка товаров
 */
class SynchronizeWithRetailCrmCommand extends Command
{
    protected static $defaultName = 'app:sync-retailcrm';

    private RetailAPI $api;

    private EntityManagerInterface $entityManager;

    private LockFactory $lockFactory;

    private ProductDeleteExcess $productDeleteExcess;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Инициализация команды
     *
     * @param RetailAPI              $api
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface        $logger
     * @param ProductDeleteExcess    $productDeleteExcess
     */
    public function __construct(
        RetailAPI $api,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ProductDeleteExcess $productDeleteExcess
    ) {
        $this->api = $api;
        $this->entityManager = $entityManager;
        $this->lockFactory = new LockFactory(new SemaphoreStore());
        $this->productDeleteExcess = $productDeleteExcess;

        parent::__construct();
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('site-code', InputArgument::OPTIONAL, 'Символьный код магазина')
            ->setDescription('Синхронизация данных между CRM и webshop');
    }

    /**
     * Исполнение команды
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lock = $this->lockFactory->createLock(sprintf('%s_%s', $this->getName(), $input->getArgument('site-code')));

        if (!$lock->acquire()) {
            $output->writeln('Команда уже запущена');

            return Command::FAILURE;
        }
        $start = microtime(true);
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $sites = $this->updateSites();

            // Если передан агрумент с кодом сайта, обновляем данные только по этому сайту
            $siteCodeArgument = $input->getArgument('site-code');
            if (null !== $siteCodeArgument) {
                if (!isset($sites[$siteCodeArgument])) {
                    $output->writeln('Не удалось найти сайт с указанным кодом');
                    $this->entityManager->getConnection()->commit();

                    return Command::FAILURE;
                }

                $sites = [$sites[$siteCodeArgument]];
            }
            foreach ($sites as $site) {
                $this->updateProductGroup($site);
                $this->updateProduct($site);
            }

            $this->entityManager->flush();
            $lock->release();
            $this->entityManager->getConnection()->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->getConnection()->rollBack();
            $this->logger->error('Ошибка синхронизации', ['exception' => $exception]);
            throw $exception;
        }
        $end = microtime(true);
        $time = $end - $start;
        $output->writeln('execute time: ' . $time);

        return Command::SUCCESS;
    }

    /**
     * Обновление сайтов
     *
     * @return array
     */
    private function updateSites(): array
    {
        $sitesFromApi = $this->api->getSites();
        $sites = [];

        foreach ($sitesFromApi as $siteItem) {
            $site = $this->entityManager->getRepository(Site::class)->findOneBy([
                'code' => $siteItem['code'],
            ]);

            if (!$site) {
                $site = new Site();
            }

            $site->setName($siteItem['name'])
                ->setUrl($siteItem['url'])
                ->setCode($siteItem['code'])
                ->setCountryIso($siteItem['countryIso'] ?? null);

            if (isset($siteItem['catalogLoadingAt']) && $catalogLoadingAt = \DateTime::createFromFormat('Y-m-d H:i:s', $siteItem['catalogLoadingAt'])) {
                $site->setCatalogLoadingAt($catalogLoadingAt);
            }
            if (isset($siteItem['catalogUpdatedAt']) && $catalogUpdatedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $siteItem['catalogUpdatedAt'])) {
                $site->setCatalogUpdatedAt($catalogUpdatedAt);
            }

            $this->entityManager->persist($site);

            $sites[$site->getCode()] = $site;
        }

        return $sites;
    }

    /**
     * Обновление товарных групп
     *
     * @param Site $site
     *
     * @return void
     */
    private function updateProductGroup(Site $site): void
    {
        $productGroupFromApi = $this->api->getProductGroups(['sites' => [$site->getCode()]]);
        foreach ($productGroupFromApi as $productGroupItem) {
            if (isset($productGroupItem['parentId'])) {
                $productGroupParent = $this->getProductGroupParent($site, $productGroupItem['parentId']);
            } else {
                $productGroupParent = null;
            }
            $productGroup = $this->entityManager->getRepository(ProductGroup::class)->find($productGroupItem['id']);
            if (!$productGroup) {
                $productGroup = new ProductGroup();
            }
            $productGroup->setSite($site);
            $productGroup->setParent($productGroupParent);
            $productGroup->setId($productGroupItem['id']);
            $productGroup->setName($productGroupItem['name']);
            $productGroup->setActive($productGroupItem['active']);
            $productGroup->setExternalId($productGroupItem['externalId'] ?? null);
            $this->entityManager->persist($productGroup);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Site $site
     * @param $id
     *
     * @return ProductGroup
     */
    private function getProductGroupParent(Site $site, int $id): ProductGroup
    {
        $productGroupFromApi = $this->api->getProductGroups(['ids' => [$id]]);
        if (isset($productGroupFromApi['parentId'])) {
            $productGroup = $this->getProductGroupParent($site, $productGroupFromApi['parentId']);
        } else {
            $productGroup = $this->entityManager->getRepository(ProductGroup::class)->findOneBy(['id' => $id]);
            if (!$productGroup) {
                $productGroup = new ProductGroup();
                $productGroup->setSite($site);
                $productGroup->setId($id);
                $productGroup->setName($productGroupFromApi['name']);
                $productGroup->setActive($productGroupFromApi['active']);
                $productGroup->setExternalId($productGroupFromApi['externalId'] ?? null);
                $this->entityManager->persist($productGroup);
                $this->entityManager->flush();
            }
        }

        return $productGroup;
    }

    /**
     * Обновление товаров
     *
     * @param Site $site
     *
     * @return void
     */
    private function updateProduct(Site $site): void
    {
        $productsFromApi = $this->api->getProducts(['sites' => [$site->getCode()]]);
        $i = 0;
        //echo(count($productsFromApi) . "\n");
        foreach ($productsFromApi as $productItem) {
            //echo(($i++) . ". " . $productItem['name'] . "\n");
            $product = $this->entityManager->getRepository(Product::class)->find($productItem['id']);
            if (!$product) {
                $product = new Product();
                $product->setId($productItem['id']);
                $product->setSite($site);
            }
            $product->setMinPrice($productItem['minPrice'] ?? 0);
            $product->setMaxPrice($productItem['maxPrice'] ?? 0);
            $product->setArticle($productItem['article'] ?? null);
            $product->setName($productItem['name']);
            $product->setUrl($productItem['url'] ?? null);
            $product->setImageUrl($productItem['imageUrl'] ?? null);
            $product->setDescription($productItem['description'] ?? '');
            $product->setPopular($productItem['popular'] ?? false);
            $product->setNovelty($productItem['novelty'] ?? false);
            $product->setRecommended($productItem['recommended'] ?? false);
            $product->setStock($productItem['stock'] ?? false);
            $product->setGroups($productItem['groups']);
            $product->setManufacturer($productItem['manufacturer'] ?? null);
            $product->setActive($productItem['active']);
            $product->setQuantity($productItem['quantity']);
            $product->setMarkable($productItem['markable']);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->productDeleteExcess->setProductId($productItem['id']);
            if ($product->getId()) {
                $offersFromApi = $productItem['offers'];
                foreach ($offersFromApi as $offersItem) {
                    $offer = $this->entityManager->getRepository(Offer::class)->find($offersItem['id']);
                    if (!$offer) {
                        $offer = new Offer();
                        $offer->setId($offersItem['id']);
                        $offer->setProduct($product);
                        $offer->setSite($site);
                    }
                    $offer->setName($offersItem['name']);
                    $offer->setPrice($offersItem['price'] ?? null);
                    $offer->setImages($offersItem['images']);
                    $offer->setExternalId($offersItem['externalId'] ?? null);
                    $offer->setXmlId($offersItem['xmlId'] ?? null);
                    $offer->setArticle($offersItem['article'] ?? null);
                    $offer->setPrices($offersItem['prices']);
                    $offer->setPurchasePrice($offersItem['purchasePrice'] ?? null);
                    $offer->setVatRate($offersItem['vatRate'] ?? null);
                    $offer->setProperties($offersItem['properties'] ?? []);
                    $offer->setQuantity($offersItem['quantity'] ?? null);
                    $offer->setWeight($offersItem['weight'] ?? null);
                    $offer->setLength($offersItem['length'] ?? null);
                    $offer->setWidth($offersItem['width'] ?? null);
                    $offer->setHeight($offersItem['height'] ?? null);
                    $offer->setActive($offersItem['active'] ?? null);
                    $offer->setUnit($offersItem['unit']);
                    $offer->setBarcode($offersItem['barcode'] ?? null);
                    $this->entityManager->persist($offer);
                    $this->entityManager->flush();
                    $this->productDeleteExcess->setOfferId($offersItem['id']);
                }
            }
        }
        $this->productDeleteExcess->deleteExcessOffers();
        $this->productDeleteExcess->deleteExcessProducts();
        $this->productDeleteExcess->truncate();
    }
}
