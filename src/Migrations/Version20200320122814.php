<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200320122814 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commut (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, etape_id INT NOT NULL, etat TINYINT(1) NOT NULL, deblocable TINYINT(1) NOT NULL, INDEX IDX_A8A373F0E075F7A4 (partie_id), INDEX IDX_A8A373F04A8CA2AD (etape_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etape (id INT AUTO_INCREMENT NOT NULL, scenario_id INT NOT NULL, titre VARCHAR(255) NOT NULL, descriptif LONGTEXT NOT NULL, automatique TINYINT(1) NOT NULL, INDEX IDX_285F75DDE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etape_etape (etape_source INT NOT NULL, etape_target INT NOT NULL, INDEX IDX_98C5F1A3E887B9CC (etape_source), INDEX IDX_98C5F1A3F162E943 (etape_target), PRIMARY KEY(etape_source, etape_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commut ADD CONSTRAINT FK_A8A373F0E075F7A4 FOREIGN KEY (partie_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE commut ADD CONSTRAINT FK_A8A373F04A8CA2AD FOREIGN KEY (etape_id) REFERENCES etape (id)');
        $this->addSql('ALTER TABLE etape ADD CONSTRAINT FK_285F75DDE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE etape_etape ADD CONSTRAINT FK_98C5F1A3E887B9CC FOREIGN KEY (etape_source) REFERENCES etape (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etape_etape ADD CONSTRAINT FK_98C5F1A3F162E943 FOREIGN KEY (etape_target) REFERENCES etape (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE bombe_id bombe_id INT DEFAULT NULL, CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trigg CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commut DROP FOREIGN KEY FK_A8A373F04A8CA2AD');
        $this->addSql('ALTER TABLE etape_etape DROP FOREIGN KEY FK_98C5F1A3E887B9CC');
        $this->addSql('ALTER TABLE etape_etape DROP FOREIGN KEY FK_98C5F1A3F162E943');
        $this->addSql('DROP TABLE commut');
        $this->addSql('DROP TABLE etape');
        $this->addSql('DROP TABLE etape_etape');
        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE bombe_id bombe_id INT DEFAULT NULL, CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trigg CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }
}
