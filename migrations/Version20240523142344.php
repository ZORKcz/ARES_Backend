<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240523142344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Making entity in MVP';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, ico VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, address_city VARCHAR(50) NOT NULL, address_street VARCHAR(50) NOT NULL, address_housenumber VARCHAR(10) NOT NULL, address_postal_code VARCHAR(9) NOT NULL, address_county VARCHAR(50) NOT NULL, deleted_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE company');
    }
}
