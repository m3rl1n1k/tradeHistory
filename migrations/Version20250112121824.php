<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112121824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT NOT NULL, parent_category_id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C1796A8F92 ON category (parent_category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_CATEGORY_NAME ON category (name)');
        $this->addSql('CREATE TABLE feedback (id INT NOT NULL, name VARCHAR(255) NOT NULL, message TEXT NOT NULL, date DATE NOT NULL, is_fixed BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE parent_category (id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9981B510A76ED395 ON parent_category (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_PARENT_NAME ON parent_category (name)');
        $this->addSql('CREATE TABLE transaction (id INT NOT NULL, user_id INT NOT NULL, wallet_id INT NOT NULL, category_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, type INT NOT NULL, description VARCHAR(255) DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D1A76ED395 ON transaction (user_id)');
        $this->addSql('CREATE INDEX IDX_723705D1712520F3 ON transaction (wallet_id)');
        $this->addSql('CREATE INDEX IDX_723705D112469DE2 ON transaction (category_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, currency VARCHAR(4) DEFAULT NULL, setting JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE wallet (id INT NOT NULL, user_id INT NOT NULL, number VARCHAR(15) NOT NULL, currency VARCHAR(4) DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C68921F96901F54 ON wallet (number)');
        $this->addSql('CREATE INDEX IDX_7C68921FA76ED395 ON wallet (user_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1796A8F92 FOREIGN KEY (parent_category_id) REFERENCES parent_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parent_category ADD CONSTRAINT FK_9981B510A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1796A8F92');
        $this->addSql('ALTER TABLE parent_category DROP CONSTRAINT FK_9981B510A76ED395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1712520F3');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D112469DE2');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921FA76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE parent_category');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
