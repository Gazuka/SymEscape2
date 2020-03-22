<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318120008 extends AbstractMigration
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
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trigg ADD deblocable TINYINT(1) NOT NULL, CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE bombe_id bombe_id INT DEFAULT NULL, CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trigg DROP deblocable, CHANGE scenario_id scenario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }
}
