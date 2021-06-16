<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210324132815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE products_id_from_api_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE offers_id_from_api_id_seq CASCADE');
        $this->addSql('DROP TABLE products_id_from_api');
        $this->addSql('DROP TABLE offers_id_from_api');
        $this->addSql('ALTER TABLE product_groups ALTER external_id TYPE INT');
        $this->addSql('ALTER TABLE product_groups ALTER external_id DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE products_id_from_api_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE offers_id_from_api_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE products_id_from_api (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE offers_id_from_api (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE product_groups ALTER external_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product_groups ALTER external_id DROP DEFAULT');
    }
}
