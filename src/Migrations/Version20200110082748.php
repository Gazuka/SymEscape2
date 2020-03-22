<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200110082748 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bombe (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, duration TIME NOT NULL, pince TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fil (id INT AUTO_INCREMENT NOT NULL, bombe_id INT DEFAULT NULL, couleur VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_4320931D48726719 (bombe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, bombe_id INT DEFAULT NULL, start DATETIME NOT NULL, UNIQUE INDEX UNIQ_232B318C48726719 (bombe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, code_barre VARCHAR(255) NOT NULL, INDEX IDX_FD71A9C5E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vis (id INT AUTO_INCREMENT NOT NULL, bombe_id INT DEFAULT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_D20E3D9848726719 (bombe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fil ADD CONSTRAINT FK_4320931D48726719 FOREIGN KEY (bombe_id) REFERENCES bombe (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C48726719 FOREIGN KEY (bombe_id) REFERENCES bombe (id)');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C5E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE vis ADD CONSTRAINT FK_D20E3D9848726719 FOREIGN KEY (bombe_id) REFERENCES bombe (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fil DROP FOREIGN KEY FK_4320931D48726719');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C48726719');
        $this->addSql('ALTER TABLE vis DROP FOREIGN KEY FK_D20E3D9848726719');
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C5E48FD905');
        $this->addSql('DROP TABLE bombe');
        $this->addSql('DROP TABLE fil');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE vis');
    }
}
