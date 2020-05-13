<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200513130848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aide (id INT AUTO_INCREMENT NOT NULL, objet_scenario_id INT DEFAULT NULL, demandeurs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_D99184A1504DCF6F (objet_scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aide_indice (aide_id INT NOT NULL, indice_id INT NOT NULL, INDEX IDX_12859A71661C0C23 (aide_id), INDEX IDX_12859A71C8C0B132 (indice_id), PRIMARY KEY(aide_id, indice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bombe (id INT AUTO_INCREMENT NOT NULL, start DATETIME DEFAULT NULL, duration INT NOT NULL, pince TINYINT(1) NOT NULL, duree_fin INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boulon (id INT AUTO_INCREMENT NOT NULL, bombe_id INT NOT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_BADFB6B348726719 (bombe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commut (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, etape_id INT NOT NULL, etat TINYINT(1) NOT NULL, deblocable TINYINT(1) NOT NULL, horaire_changement DATETIME NOT NULL, INDEX IDX_A8A373F0E48FD905 (game_id), INDEX IDX_A8A373F04A8CA2AD (etape_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etape (id INT AUTO_INCREMENT NOT NULL, scenario_id INT NOT NULL, titre VARCHAR(255) NOT NULL, descriptif LONGTEXT NOT NULL, automatique TINYINT(1) NOT NULL, INDEX IDX_285F75DDE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etape_etape (etape_source INT NOT NULL, etape_target INT NOT NULL, INDEX IDX_98C5F1A3E887B9CC (etape_source), INDEX IDX_98C5F1A3F162E943 (etape_target), PRIMARY KEY(etape_source, etape_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fil (id INT AUTO_INCREMENT NOT NULL, bombe_id INT DEFAULT NULL, couleur VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_4320931D48726719 (bombe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, scenario_id INT DEFAULT NULL, start DATETIME DEFAULT NULL, INDEX IDX_232B318CE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indice (id INT AUTO_INCREMENT NOT NULL, etape_id INT NOT NULL, descriptif LONGTEXT DEFAULT NULL, INDEX IDX_38710B554A8CA2AD (etape_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, prenom VARCHAR(255) NOT NULL, age INT DEFAULT NULL, sexe VARCHAR(255) DEFAULT NULL, code_barre VARCHAR(255) DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, INDEX IDX_FD71A9C5E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objet_scenario (id INT AUTO_INCREMENT NOT NULL, bombe_id INT DEFAULT NULL, game_id INT NOT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8F952FEA48726719 (bombe_id), INDEX IDX_8F952FEAE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aide ADD CONSTRAINT FK_D99184A1504DCF6F FOREIGN KEY (objet_scenario_id) REFERENCES objet_scenario (id)');
        $this->addSql('ALTER TABLE aide_indice ADD CONSTRAINT FK_12859A71661C0C23 FOREIGN KEY (aide_id) REFERENCES aide (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aide_indice ADD CONSTRAINT FK_12859A71C8C0B132 FOREIGN KEY (indice_id) REFERENCES indice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boulon ADD CONSTRAINT FK_BADFB6B348726719 FOREIGN KEY (bombe_id) REFERENCES bombe (id)');
        $this->addSql('ALTER TABLE commut ADD CONSTRAINT FK_A8A373F0E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE commut ADD CONSTRAINT FK_A8A373F04A8CA2AD FOREIGN KEY (etape_id) REFERENCES etape (id)');
        $this->addSql('ALTER TABLE etape ADD CONSTRAINT FK_285F75DDE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE etape_etape ADD CONSTRAINT FK_98C5F1A3E887B9CC FOREIGN KEY (etape_source) REFERENCES etape (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etape_etape ADD CONSTRAINT FK_98C5F1A3F162E943 FOREIGN KEY (etape_target) REFERENCES etape (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fil ADD CONSTRAINT FK_4320931D48726719 FOREIGN KEY (bombe_id) REFERENCES bombe (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE indice ADD CONSTRAINT FK_38710B554A8CA2AD FOREIGN KEY (etape_id) REFERENCES etape (id)');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C5E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE objet_scenario ADD CONSTRAINT FK_8F952FEA48726719 FOREIGN KEY (bombe_id) REFERENCES bombe (id)');
        $this->addSql('ALTER TABLE objet_scenario ADD CONSTRAINT FK_8F952FEAE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aide_indice DROP FOREIGN KEY FK_12859A71661C0C23');
        $this->addSql('ALTER TABLE boulon DROP FOREIGN KEY FK_BADFB6B348726719');
        $this->addSql('ALTER TABLE fil DROP FOREIGN KEY FK_4320931D48726719');
        $this->addSql('ALTER TABLE objet_scenario DROP FOREIGN KEY FK_8F952FEA48726719');
        $this->addSql('ALTER TABLE commut DROP FOREIGN KEY FK_A8A373F04A8CA2AD');
        $this->addSql('ALTER TABLE etape_etape DROP FOREIGN KEY FK_98C5F1A3E887B9CC');
        $this->addSql('ALTER TABLE etape_etape DROP FOREIGN KEY FK_98C5F1A3F162E943');
        $this->addSql('ALTER TABLE indice DROP FOREIGN KEY FK_38710B554A8CA2AD');
        $this->addSql('ALTER TABLE commut DROP FOREIGN KEY FK_A8A373F0E48FD905');
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C5E48FD905');
        $this->addSql('ALTER TABLE objet_scenario DROP FOREIGN KEY FK_8F952FEAE48FD905');
        $this->addSql('ALTER TABLE aide_indice DROP FOREIGN KEY FK_12859A71C8C0B132');
        $this->addSql('ALTER TABLE aide DROP FOREIGN KEY FK_D99184A1504DCF6F');
        $this->addSql('ALTER TABLE etape DROP FOREIGN KEY FK_285F75DDE04E49DF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE04E49DF');
        $this->addSql('DROP TABLE aide');
        $this->addSql('DROP TABLE aide_indice');
        $this->addSql('DROP TABLE bombe');
        $this->addSql('DROP TABLE boulon');
        $this->addSql('DROP TABLE commut');
        $this->addSql('DROP TABLE etape');
        $this->addSql('DROP TABLE etape_etape');
        $this->addSql('DROP TABLE fil');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE indice');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE objet_scenario');
        $this->addSql('DROP TABLE scenario');
    }
}
