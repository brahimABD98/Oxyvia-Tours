<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330142621 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, idhotel_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, numero INT NOT NULL, occupe VARCHAR(255) NOT NULL, prix INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_C509E4FFB83297E7 (reservation_id), INDEX IDX_C509E4FF9896F315 (idhotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, daten DATE NOT NULL, num INT NOT NULL, email VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, cin INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, idhotel_id INT DEFAULT NULL, idclient_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9474526C9896F315 (idhotel_id), INDEX IDX_9474526C67F0C0D4 (idclient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE craue_formflowdemo_driver (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE craue_formflowdemo_location (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) NOT NULL, region VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE craue_formflowdemo_topic (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, category VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, details LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE craue_formflowdemo_verhicle (id INT AUTO_INCREMENT NOT NULL, driver_id INT DEFAULT NULL, number_of_wheels INT NOT NULL, engine VARCHAR(255) DEFAULT NULL, INDEX IDX_40A375F5C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, nb_chambre_dispo INT NOT NULL, pays VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, nbetoile INT NOT NULL, num INT NOT NULL, email VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, altitude VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, hotel_id INT DEFAULT NULL, voyage_id INT DEFAULT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, prix INT NOT NULL, nb_adulte INT NOT NULL, type VARCHAR(255) NOT NULL, nb_enfants INT NOT NULL, nb_chambre_single_reserve INT NOT NULL, nb_chambre_double_reserve INT NOT NULL, confirme VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_42C8495519EB6921 (client_id), INDEX IDX_42C849553243BB18 (hotel_id), INDEX IDX_42C8495568C9E5AF (voyage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transport (id INT AUTO_INCREMENT NOT NULL, voyage_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, matricule VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, prix INT NOT NULL, INDEX IDX_66AB212E68C9E5AF (voyage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voyage (id INT AUTO_INCREMENT NOT NULL, hotel_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, prix_personne INT NOT NULL, nb_personne INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_3F9D89553243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voyage_place (voyage_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_7E8D6F2268C9E5AF (voyage_id), INDEX IDX_7E8D6F22DA6A219 (place_id), PRIMARY KEY(voyage_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FFB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF9896F315 FOREIGN KEY (idhotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9896F315 FOREIGN KEY (idhotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C67F0C0D4 FOREIGN KEY (idclient_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE craue_formflowdemo_verhicle ADD CONSTRAINT FK_40A375F5C3423909 FOREIGN KEY (driver_id) REFERENCES craue_formflowdemo_driver (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495568C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE transport ADD CONSTRAINT FK_66AB212E68C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT FK_3F9D89553243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE voyage_place ADD CONSTRAINT FK_7E8D6F2268C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE voyage_place ADD CONSTRAINT FK_7E8D6F22DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C67F0C0D4');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE craue_formflowdemo_verhicle DROP FOREIGN KEY FK_40A375F5C3423909');
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF9896F315');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C9896F315');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849553243BB18');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY FK_3F9D89553243BB18');
        $this->addSql('ALTER TABLE voyage_place DROP FOREIGN KEY FK_7E8D6F22DA6A219');
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FFB83297E7');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495568C9E5AF');
        $this->addSql('ALTER TABLE transport DROP FOREIGN KEY FK_66AB212E68C9E5AF');
        $this->addSql('ALTER TABLE voyage_place DROP FOREIGN KEY FK_7E8D6F2268C9E5AF');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE craue_formflowdemo_driver');
        $this->addSql('DROP TABLE craue_formflowdemo_location');
        $this->addSql('DROP TABLE craue_formflowdemo_topic');
        $this->addSql('DROP TABLE craue_formflowdemo_verhicle');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE transport');
        $this->addSql('DROP TABLE voyage');
        $this->addSql('DROP TABLE voyage_place');
    }
}
