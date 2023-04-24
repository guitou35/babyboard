<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406205244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE sleep_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sleep (id INT NOT NULL, children_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, time_sleep BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F33C2AC3D3D2749 ON sleep (children_id)');
        $this->addSql('CREATE INDEX IDX_F33C2AC7E3C61F9 ON sleep (owner_id)');
        $this->addSql('COMMENT ON COLUMN sleep.children_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sleep.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE sleep ADD CONSTRAINT FK_F33C2AC3D3D2749 FOREIGN KEY (children_id) REFERENCES "children" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sleep ADD CONSTRAINT FK_F33C2AC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE children ALTER birthdate TYPE DATE');
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE sleep_id_seq CASCADE');
        $this->addSql('ALTER TABLE sleep DROP CONSTRAINT FK_F33C2AC3D3D2749');
        $this->addSql('ALTER TABLE sleep DROP CONSTRAINT FK_F33C2AC7E3C61F9');
        $this->addSql('DROP TABLE sleep');
        $this->addSql('ALTER TABLE "children" ALTER birthdate TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE "children" ALTER parents DROP NOT NULL');
        $this->addSql('ALTER TABLE "children" ALTER nounou DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER type_user DROP NOT NULL');
    }
}
