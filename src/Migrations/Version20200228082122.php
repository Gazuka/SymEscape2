<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200228082122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `trigger` (id INT AUTO_INCREMENT NOT NULL, scenario_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_1A6B0F5DE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trigger_trigger (trigger_source INT NOT NULL, trigger_target INT NOT NULL, INDEX IDX_1E2B0864C4AB53F5 (trigger_source), INDEX IDX_1E2B0864DD4E037A (trigger_target), PRIMARY KEY(trigger_source, trigger_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `trigger` ADD CONSTRAINT FK_1A6B0F5DE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE trigger_trigger ADD CONSTRAINT FK_1E2B0864C4AB53F5 FOREIGN KEY (trigger_source) REFERENCES `trigger` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trigger_trigger ADD CONSTRAINT FK_1E2B0864DD4E037A FOREIGN KEY (trigger_target) REFERENCES `trigger` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('CREATE INDEX IDX_232B318CE04E49DF ON game (scenario_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE04E49DF');
        $this->addSql('ALTER TABLE `trigger` DROP FOREIGN KEY FK_1A6B0F5DE04E49DF');
        $this->addSql('ALTER TABLE trigger_trigger DROP FOREIGN KEY FK_1E2B0864C4AB53F5');
        $this->addSql('ALTER TABLE trigger_trigger DROP FOREIGN KEY FK_1E2B0864DD4E037A');
        $this->addSql('DROP TABLE scenario');
        $this->addSql('DROP TABLE `trigger`');
        $this->addSql('DROP TABLE trigger_trigger');
        $this->addSql('DROP INDEX IDX_232B318CE04E49DF ON game');
        $this->addSql('ALTER TABLE game DROP scenario_id');
    }
}
