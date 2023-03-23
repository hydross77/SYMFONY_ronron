<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323123336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP type, DROP date_cat, DROP cp, DROP street, DROP city, DROP country, DROP description, DROP state');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD type VARCHAR(255) NOT NULL, ADD date_cat DATE NOT NULL, ADD cp VARCHAR(255) NOT NULL, ADD street VARCHAR(255) DEFAULT NULL, ADD city VARCHAR(255) NOT NULL, ADD country VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD state VARCHAR(255) DEFAULT NULL');
    }
}
