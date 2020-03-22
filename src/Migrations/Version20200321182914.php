<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200321182914 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE commut DROP FOREIGN KEY FK_A8A373F0E075F7A4');
        $this->addSql('DROP INDEX IDX_A8A373F0E075F7A4 ON commut');
        $this->addSql('ALTER TABLE commut CHANGE partie_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE commut ADD CONSTRAINT FK_A8A373F0E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_A8A373F0E48FD905 ON commut (game_id)');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL, CHANGE code_barre code_barre VARCHAR(255) DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE sexe sexe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE objet_scenario CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trigg CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE commut DROP FOREIGN KEY FK_A8A373F0E48FD905');
        $this->addSql('DROP INDEX IDX_A8A373F0E48FD905 ON commut');
        $this->addSql('ALTER TABLE commut CHANGE game_id partie_id INT NOT NULL');
        $this->addSql('ALTER TABLE commut ADD CONSTRAINT FK_A8A373F0E075F7A4 FOREIGN KEY (partie_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_A8A373F0E075F7A4 ON commut (partie_id)');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE sexe sexe VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE code_barre code_barre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE objet_scenario CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trigg CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }
}
