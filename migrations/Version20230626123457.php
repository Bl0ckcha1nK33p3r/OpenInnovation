<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230626123457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create construction_plan table with image_path column';
    }

    public function up(Schema $schema) : void
    {
        // This up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE construction_plan (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL
        )');
    }

    public function down(Schema $schema) : void
    {
        // This down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE construction_plan');
    }
}