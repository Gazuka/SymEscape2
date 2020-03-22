<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200228121347 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trigger_trigger DROP FOREIGN KEY FK_1E2B0864C4AB53F5');
        $this->addSql('ALTER TABLE trigger_trigger DROP FOREIGN KEY FK_1E2B0864DD4E037A');
        $this->addSql('CREATE TABLE trigg (id INT AUTO_INCREMENT NOT NULL, scenario_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_B52A2031E04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trigg_trigg (trigg_source INT NOT NULL, trigg_target INT NOT NULL, INDEX IDX_3DEF4400EDCBA2D (trigg_source), INDEX IDX_3DEF44001739EAA2 (trigg_target), PRIMARY KEY(trigg_source, trigg_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trigg ADD CONSTRAINT FK_B52A2031E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE trigg_trigg ADD CONSTRAINT FK_3DEF4400EDCBA2D FOREIGN KEY (trigg_source) REFERENCES trigg (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trigg_trigg ADD CONSTRAINT FK_3DEF44001739EAA2 FOREIGN KEY (trigg_target) REFERENCES trigg (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE `trigger`');
        $this->addSql('DROP TABLE trigger_trigger');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trigg_trigg DROP FOREIGN KEY FK_3DEF4400EDCBA2D');
        $this->addSql('ALTER TABLE trigg_trigg DROP FOREIGN KEY FK_3DEF44001739EAA2');
        $this->addSql('CREATE TABLE `trigger` (id INT AUTO_INCREMENT NOT NULL, scenario_id INT DEFAULT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, etat TINYINT(1) NOT NULL, INDEX IDX_1A6B0F5DE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trigger_trigger (trigger_source INT NOT NULL, trigger_target INT NOT NULL, INDEX IDX_1E2B0864C4AB53F5 (trigger_source), INDEX IDX_1E2B0864DD4E037A (trigger_target), PRIMARY KEY(trigger_source, trigger_target)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `trigger` ADD CONSTRAINT FK_1A6B0F5DE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE trigger_trigger ADD CONSTRAINT FK_1E2B0864C4AB53F5 FOREIGN KEY (trigger_source) REFERENCES `trigger` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trigger_trigger ADD CONSTRAINT FK_1E2B0864DD4E037A FOREIGN KEY (trigger_target) REFERENCES `trigger` (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE trigg');
        $this->addSql('DROP TABLE trigg_trigg');
    }
}
