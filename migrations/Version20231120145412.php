<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120145412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, type INT NOT NULL, date DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_723705D19D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE deposit DROP status, DROP start_amount, CHANGE amount amount DOUBLE PRECISION NOT NULL, CHANGE percent user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D399D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_95DB9D399D86650F ON deposit (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19D86650F');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D399D86650F');
        $this->addSql('DROP INDEX IDX_95DB9D399D86650F ON deposit');
        $this->addSql('ALTER TABLE deposit ADD status VARCHAR(25) NOT NULL, ADD start_amount DOUBLE PRECISION NOT NULL, CHANGE amount amount DOUBLE PRECISION DEFAULT NULL, CHANGE user_id_id percent INT NOT NULL');
    }
}
