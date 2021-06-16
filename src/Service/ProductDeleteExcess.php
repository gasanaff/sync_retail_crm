<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Connection;

class ProductDeleteExcess
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $create_products_sql = 'CREATE TABLE IF NOT EXISTS products_id_from_api (id serial PRIMARY KEY)';
        $create_offers_sql = 'CREATE TABLE IF NOT EXISTS offers_id_from_api (id serial PRIMARY KEY)';
        $this->connection->exec($create_products_sql);
        $this->connection->exec($create_offers_sql);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setProductId(int $id): bool
    {
        $sql = "INSERT INTO products_id_from_api (id) VALUES ('{$id}')";
        if ($this->connection->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setOfferId(int $id): bool
    {
        $sql = "INSERT INTO offers_id_from_api (id) VALUES ('{$id}')";
        if ($this->connection->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteExcessOffers(): bool
    {
        $sql = 'DELETE FROM offers WHERE id NOT IN (SELECT id FROM offers_id_from_api)';
        if ($this->connection->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteExcessProducts(): bool
    {
        $sql = 'UPDATE products SET deleted_at = current_timestamp WHERE id NOT IN (SELECT id FROM products_id_from_api)';
        if ($this->connection->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function truncate(): bool
    {
        $sql = 'TRUNCATE products_id_from_api, offers_id_from_api';
        if ($this->connection->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }
}
