<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220141731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE change (id UUID NOT NULL, children_id UUID NOT NULL, owner_id UUID NOT NULL, type_change VARCHAR(255) NOT NULL, heure TIME(0) WITHOUT TIME ZONE NOT NULL, contenu JSON NOT NULL, problems JSON DEFAULT NULL, products JSON NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4057FE203D3D2749 ON change (children_id)');
        $this->addSql('CREATE INDEX IDX_4057FE207E3C61F9 ON change (owner_id)');
        $this->addSql('COMMENT ON COLUMN change.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN change.children_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN change.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "children" (id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, birthdate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, weight INT NOT NULL, size INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "children".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE repas (id UUID NOT NULL, children_id UUID NOT NULL, owner_id UUID NOT NULL, aliment_name VARCHAR(255) NOT NULL, repas_time VARCHAR(255) NOT NULL, quantity VARCHAR(255) DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8D351B33D3D2749 ON repas (children_id)');
        $this->addSql('CREATE INDEX IDX_A8D351B37E3C61F9 ON repas (owner_id)');
        $this->addSql('COMMENT ON COLUMN repas.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN repas.children_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN repas.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_nounou BOOLEAN NOT NULL, phone VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_children (user_id UUID NOT NULL, children_id UUID NOT NULL, PRIMARY KEY(user_id, children_id))');
        $this->addSql('CREATE INDEX IDX_411A55BAA76ED395 ON user_children (user_id)');
        $this->addSql('CREATE INDEX IDX_411A55BA3D3D2749 ON user_children (children_id)');
        $this->addSql('COMMENT ON COLUMN user_children.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_children.children_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE change ADD CONSTRAINT FK_4057FE203D3D2749 FOREIGN KEY (children_id) REFERENCES "children" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE change ADD CONSTRAINT FK_4057FE207E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B33D3D2749 FOREIGN KEY (children_id) REFERENCES "children" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_children ADD CONSTRAINT FK_411A55BAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_children ADD CONSTRAINT FK_411A55BA3D3D2749 FOREIGN KEY (children_id) REFERENCES "children" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('ALTER TABLE change DROP CONSTRAINT FK_4057FE203D3D2749');
        $this->addSql('ALTER TABLE change DROP CONSTRAINT FK_4057FE207E3C61F9');
        $this->addSql('ALTER TABLE repas DROP CONSTRAINT FK_A8D351B33D3D2749');
        $this->addSql('ALTER TABLE repas DROP CONSTRAINT FK_A8D351B37E3C61F9');
        $this->addSql('ALTER TABLE user_children DROP CONSTRAINT FK_411A55BAA76ED395');
        $this->addSql('ALTER TABLE user_children DROP CONSTRAINT FK_411A55BA3D3D2749');
        $this->addSql('DROP TABLE change');
        $this->addSql('DROP TABLE "children"');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE repas');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_children');
    }
}
