<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200327142751 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aide (id INT AUTO_INCREMENT NOT NULL, objet_scenario_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D99184A1504DCF6F (objet_scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aide_indice (aide_id INT NOT NULL, indice_id INT NOT NULL, INDEX IDX_12859A71661C0C23 (aide_id), INDEX IDX_12859A71C8C0B132 (indice_id), PRIMARY KEY(aide_id, indice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aide ADD CONSTRAINT FK_D99184A1504DCF6F FOREIGN KEY (objet_scenario_id) REFERENCES objet_scenario (id)');
        $this->addSql('ALTER TABLE aide_indice ADD CONSTRAINT FK_12859A71661C0C23 FOREIGN KEY (aide_id) REFERENCES aide (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aide_indice ADD CONSTRAINT FK_12859A71C8C0B132 FOREIGN KEY (indice_id) REFERENCES indice (id) ON DELETE CASCADE');
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

        $this->addSql('ALTER TABLE aide_indice DROP FOREIGN KEY FK_12859A71661C0C23');
        $this->addSql('DROP TABLE aide');
        $this->addSql('DROP TABLE aide_indice');
        $this->addSql('ALTER TABLE bombe CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fil CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE scenario_id scenario_id INT DEFAULT NULL, CHANGE start start DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE joueur CHANGE game_id game_id INT DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE sexe sexe VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE code_barre code_barre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE objet_scenario CHANGE bombe_id bombe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vis CHANGE bombe_id bombe_id INT DEFAULT NULL');
    }
}
