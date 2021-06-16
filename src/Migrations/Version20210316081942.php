<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210316081942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offers (id INT NOT NULL, product_id INT NOT NULL, site_id INT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, images JSONB NOT NULL, external_id VARCHAR(255) DEFAULT NULL, xml_id VARCHAR(255) DEFAULT NULL, article VARCHAR(255) DEFAULT NULL, prices JSONB NOT NULL, purchase_price NUMERIC(10, 2) DEFAULT NULL, vat_rate VARCHAR(255) DEFAULT NULL, properties JSONB DEFAULT NULL, quantity NUMERIC(10, 2) DEFAULT NULL, weight NUMERIC(10, 3) DEFAULT NULL, length NUMERIC(10, 3) DEFAULT NULL, width NUMERIC(10, 3) DEFAULT NULL, height NUMERIC(10, 3) DEFAULT NULL, active BOOLEAN DEFAULT NULL, unit JSONB DEFAULT NULL, barcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA4604274584665A ON offers (product_id)');
        $this->addSql('CREATE INDEX IDX_DA460427F6BD1646 ON offers (site_id)');
        $this->addSql('CREATE TABLE product_groups (id INT NOT NULL, site_id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, external_id VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_921178D4F6BD1646 ON product_groups (site_id)');
        $this->addSql('CREATE INDEX IDX_921178D4727ACA70 ON product_groups (parent_id)');
        $this->addSql('CREATE TABLE products (id INT NOT NULL, site_id INT NOT NULL, min_price NUMERIC(10, 2) NOT NULL, max_price NUMERIC(10, 2) NOT NULL, article VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(510) DEFAULT NULL, image_url VARCHAR(510) DEFAULT NULL, description VARCHAR(510) NOT NULL, popular BOOLEAN DEFAULT NULL, novelty BOOLEAN DEFAULT NULL, recommended BOOLEAN DEFAULT NULL, stock BOOLEAN DEFAULT NULL, groups JSONB NOT NULL, manufacturer VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT NULL, quantity INT DEFAULT NULL, markable BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AF6BD1646 ON products (site_id)');
        $this->addSql('CREATE TABLE sites (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(510) NOT NULL, catalog_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, catalog_loading_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, country_iso VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA4604274584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA460427F6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_groups ADD CONSTRAINT FK_921178D4F6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_groups ADD CONSTRAINT FK_921178D4727ACA70 FOREIGN KEY (parent_id) REFERENCES product_groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AF6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_groups DROP CONSTRAINT FK_921178D4727ACA70');
        $this->addSql('ALTER TABLE offers DROP CONSTRAINT FK_DA4604274584665A');
        $this->addSql('ALTER TABLE offers DROP CONSTRAINT FK_DA460427F6BD1646');
        $this->addSql('ALTER TABLE product_groups DROP CONSTRAINT FK_921178D4F6BD1646');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5AF6BD1646');
        $this->addSql('DROP TABLE offers');
        $this->addSql('DROP TABLE product_groups');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE sites');
    }
}
