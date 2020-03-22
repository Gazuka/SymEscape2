<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200320171538 extends AbstractMigration
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
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE bombe_id bombe_id INT DEFAULT NULL, CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE joueur ADD age INT DEFAULT NULL, ADD sexe VARCHAR(255) DEFAULT NULL, CHANGE game_id game_id INT DEFAULT NULL, CHANGE code_barre code_barre VARCHAR(255) DEFAULT NULL, CHANGE nom prenom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE trigg CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE bombe_id bombe_id INT DEFAULT NULL, CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE joueur DROP age, DROP sexe, CHANGE game_id game_id INT DEFAULT NULL, CHANGE code_barre code_barre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE prenom nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE trigg CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }
}
