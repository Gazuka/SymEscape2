<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200322183952 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trigg_trigg DROP FOREIGN KEY FK_3DEF44001739EAA2');
        $this->addSql('ALTER TABLE trigg_trigg DROP FOREIGN KEY FK_3DEF4400EDCBA2D');
        $this->addSql('DROP TABLE trigg');
        $this->addSql('DROP TABLE trigg_trigg');
        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL, CHANGE code_barre code_barre VARCHAR(255) DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE sexe sexe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE objet_scenario CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE trigg (id INT AUTO_INCREMENT NOT NULL, scenario_id INT DEFAULT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, etat TINYINT(1) NOT NULL, descriptif LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, deblocable TINYINT(1) NOT NULL, automatique TINYINT(1) NOT NULL, INDEX IDX_B52A2031E04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trigg_trigg (trigg_source INT NOT NULL, trigg_target INT NOT NULL, INDEX IDX_3DEF44001739EAA2 (trigg_target), INDEX IDX_3DEF4400EDCBA2D (trigg_source), PRIMARY KEY(trigg_source, trigg_target)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE trigg ADD CONSTRAINT FK_B52A2031E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE trigg_trigg ADD CONSTRAINT FK_3DEF44001739EAA2 FOREIGN KEY (trigg_target) REFERENCES trigg (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trigg_trigg ADD CONSTRAINT FK_3DEF4400EDCBA2D FOREIGN KEY (trigg_source) REFERENCES trigg (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE sexe sexe VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE code_barre code_barre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE objet_scenario CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }
}
