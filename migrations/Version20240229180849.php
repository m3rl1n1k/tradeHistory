<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229180849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer ADD from_wallet_id INT NOT NULL, ADD to_wallet_id INT NOT NULL, DROP from_wallet, DROP to_wallet');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C061B9B549 FOREIGN KEY (from_wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C04086F782 FOREIGN KEY (to_wallet_id) REFERENCES wallet (id)');
        $this->addSql('CREATE INDEX IDX_4034A3C061B9B549 ON transfer (from_wallet_id)');
        $this->addSql('CREATE INDEX IDX_4034A3C04086F782 ON transfer (to_wallet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C061B9B549');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C04086F782');
        $this->addSql('DROP INDEX IDX_4034A3C061B9B549 ON transfer');
        $this->addSql('DROP INDEX IDX_4034A3C04086F782 ON transfer');
        $this->addSql('ALTER TABLE transfer ADD from_wallet INT NOT NULL, ADD to_wallet INT NOT NULL, DROP from_wallet_id, DROP to_wallet_id');
    }
}
