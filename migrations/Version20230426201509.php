<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426201509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE change ADD date_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE sleep_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE "children" ALTER parents DROP NOT NULL');
        $this->addSql('ALTER TABLE "children" ALTER nounou DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER type_user DROP NOT NULL');
        $this->addSql('ALTER TABLE sleep DROP date_at');
        $this->addSql('ALTER TABLE sleep DROP created_at');
        $this->addSql('ALTER TABLE sleep DROP updated_at');
        $this->addSql('ALTER TABLE sleep ALTER id TYPE INT');
        $this->addSql('COMMENT ON COLUMN sleep.id IS NULL');
        $this->addSql('ALTER TABLE change DROP date_at');
    }
}
