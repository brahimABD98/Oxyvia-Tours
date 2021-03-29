<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210306205930 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_personnel ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD occupation VARCHAR(255) NOT NULL, ADD salaire_annuel VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE depense CHANGE id_personnel_id id_personnel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE voyage ADD ville VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL, ADD date_debut DATE NOT NULL, ADD date_fin VARCHAR(255) NOT NULL, ADD prix_personne VARCHAR(255) NOT NULL, ADD nb_personne INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_personnel DROP nom, DROP prenom, DROP occupation, DROP salaire_annuel');
        $this->addSql('ALTER TABLE depense CHANGE id_personnel_id id_personnel_id INT NOT NULL');
        $this->addSql('ALTER TABLE voyage DROP ville, DROP description, DROP date_debut, DROP date_fin, DROP prix_personne, DROP nb_personne');
    }
}
