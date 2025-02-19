<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219175543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE budget_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE transfer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE budget (id INT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL, planing_amount DOUBLE PRECISION NOT NULL, actual_amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_73F2F77BA76ED395 ON budget (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73F2F77B12469DE2 ON budget (category_id)');
        $this->addSql('CREATE TABLE transfer (id INT NOT NULL, wallet_out_id INT NOT NULL, wallet_in_id INT NOT NULL, user_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4034A3C0B4AFC73E ON transfer (wallet_out_id)');
        $this->addSql('CREATE INDEX IDX_4034A3C060FE1849 ON transfer (wallet_in_id)');
        $this->addSql('CREATE INDEX IDX_4034A3C0A76ED395 ON transfer (user_id)');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0B4AFC73E FOREIGN KEY (wallet_out_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C060FE1849 FOREIGN KEY (wallet_in_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_setting ADD show_color_in_transaction_list BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE user_setting ADD transaction_column_show JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet ADD is_main BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE budget_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE transfer_id_seq CASCADE');
        $this->addSql('ALTER TABLE budget DROP CONSTRAINT FK_73F2F77BA76ED395');
        $this->addSql('ALTER TABLE budget DROP CONSTRAINT FK_73F2F77B12469DE2');
        $this->addSql('ALTER TABLE transfer DROP CONSTRAINT FK_4034A3C0B4AFC73E');
        $this->addSql('ALTER TABLE transfer DROP CONSTRAINT FK_4034A3C060FE1849');
        $this->addSql('ALTER TABLE transfer DROP CONSTRAINT FK_4034A3C0A76ED395');
        $this->addSql('DROP TABLE budget');
        $this->addSql('DROP TABLE transfer');
        $this->addSql('ALTER TABLE wallet DROP is_main');
        $this->addSql('ALTER TABLE user_setting DROP show_color_in_transaction_list');
        $this->addSql('ALTER TABLE user_setting DROP transaction_column_show');
    }
}
