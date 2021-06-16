<?php
namespace App\Tests\unit;

use App\Tests\UnitTester;

class ComposerCest
{
    public function _before(UnitTester $I)
    {
    }

    /**
     * Проверяет что пакеты в composer.json отсортированы
     * @param UnitTester $I
     */
    public function tryToTestSortPackages(UnitTester $I)
    {
        $rootDir = realpath(__DIR__ .'/../..');

        $I->assertNotEquals('/', $rootDir);

        $composerJson = $rootDir . '/composer.json';
        $I->assertFileExists($composerJson);
        $composerJson = json_decode(file_get_contents($composerJson), true);

        $this->assertPackagesSort(array_keys($composerJson['require']), $I);
        $this->assertPackagesSort(array_keys($composerJson['require-dev']), $I);
    }

    protected function assertPackagesSort(array $names, UnitTester $I): void
    {
        // php должен быть последним
        if (\in_array('php', $names, true)) {
            $first = array_shift($names);

            $I->assertSame('php', $first);
        }

        // расширения в начале
        $lastExt = -1;

        for ($i = 0; $i <= \count($names) - 1; $i++) {

            $name = $names[$i];

            if (0 === strpos($name, 'ext-')) {
                $lastExt = $i + 1;
            } else {
                break;
            }
        }

        if ($lastExt >= 0) {
            $exts = \array_slice($names, 0, $lastExt);
            $names = \array_slice($names,  $lastExt);
        } else {
            $exts = [];
        }

        // в серединке расширений не должно быть
        foreach ($names as $name) {
            $I->assertStringStartsNotWith('ext-', $name);
        }

        $this->assertSorted($exts, $I);
        $this->assertSorted($names, $I);
    }

    protected function assertSorted(array $names, $I): void
    {
        $names = array_flip($names);
        ksort($names);

        $expectedPos = 0;
        foreach ($names as $name => $oldPos) {
            if ($expectedPos !== $oldPos) {
                $I->fail("Package $name on wrong place. " . var_export($names, true));
            }
            ++$expectedPos;
        }
    }

    /**
     * Проверяет что composer.lock и composer.json синхронизированы (по хешу)
     */
    public function testJsonAndLockInSync(UnitTester $I)
    {
        $rootDir = realpath(__DIR__ .'/../..');

        $I->assertNotEquals('/', $rootDir);

        $lockSum = $this->getLockHash($rootDir . '/composer.lock', $I);
        $jsonSum = $this->getJsonHash($rootDir . '/composer.json', $I);

        if ($lockSum !== $jsonSum) {
            $I->fail(
                'The lock file is not up to date with the latest changes in composer.json. ' .
                'You may be getting outdated dependencies. Please check it manually.'
            );
        }
    }

    protected function getJsonHash($file, UnitTester $I)
    {
        $I->assertFileExists($file);

        $data = file_get_contents($file);
        $I->assertJson($data);

        return self::getContentHash($data);
    }

    protected function getLockHash($file, UnitTester $I)
    {
        $I->assertFileExists($file);

        $data = file_get_contents($file);
        $I->assertJson($data);

        $data = json_decode($data, true);
        $I->assertNotNull($data);
        $I->assertArrayHasKey('content-hash', $data);

        return $data['content-hash'];
    }

    /**
     * @see https://github.com/composer/composer/blob/1.3.0/src/Composer/Package/Locker.php#L72
     *
     * @param string $composerFileContents
     *
     * @return string
     */
    private static function getContentHash($composerFileContents)
    {
        $content = json_decode($composerFileContents, true);
        $relevantKeys = [
            'name',
            'version',
            'require',
            'require-dev',
            'conflict',
            'replace',
            'provide',
            'minimum-stability',
            'prefer-stable',
            'repositories',
            'extra',
        ];
        $relevantContent = [];
        foreach (array_intersect($relevantKeys, array_keys($content)) as $key) {
            $relevantContent[$key] = $content[$key];
        }
        if (isset($content['config']['platform'])) {
            $relevantContent['config']['platform'] = $content['config']['platform'];
        }
        ksort($relevantContent);

        return md5(json_encode($relevantContent));
    }
}
