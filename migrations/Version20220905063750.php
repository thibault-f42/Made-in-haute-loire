<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220905063750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(12) NOT NULL, email VARCHAR(255) NOT NULL, siret VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_D19FA6026E94372 (siret), INDEX IDX_D19FA60A73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etat_commande (id INT AUTO_INCREMENT NOT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, url_fichier VARCHAR(255) NOT NULL, type_fichier VARCHAR(255) NOT NULL, INDEX IDX_9B76551FA4AEAFEA (entreprise_id), INDEX IDX_9B76551FF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, sous_categorie_id INT NOT NULL, categorie_id INT NOT NULL, nom_article VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix DOUBLE PRECISION NOT NULL, etat_vente VARCHAR(255) NOT NULL, stock INT NOT NULL, code_produit VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27A4AEAFEA (entreprise_id), INDEX IDX_29A5EC27365BF48 (sous_categorie_id), INDEX IDX_29A5EC27BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_categorie (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_52743D7BBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_commande (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, commande_id INT NOT NULL, entreprise_id INT NOT NULL, etat_id INT DEFAULT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, INDEX IDX_66A49228FB88E14F (utilisateur_id), INDEX IDX_66A4922882EA2E54 (commande_id), INDEX IDX_66A49228A4AEAFEA (entreprise_id), INDEX IDX_66A49228D5E86FF (etat_id), INDEX IDX_66A49228F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, entreprise_id INT DEFAULT NULL, adresse_livraison_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, vendeur TINYINT(1) NOT NULL, adresse VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, activation_token VARCHAR(255) DEFAULT NULL, token_mdp VARCHAR(255) DEFAULT NULL, actif TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3A73F0036 (ville_id), UNIQUE INDEX UNIQ_1D1C63B3A4AEAFEA (entreprise_id), INDEX IDX_1D1C63B3BE2F0A35 (adresse_livraison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, canton_id INT NOT NULL, departement_id INT NOT NULL, nom VARCHAR(255) NOT NULL, code_postal VARCHAR(5) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, INDEX IDX_43C3D9C38D070D0B (canton_id), INDEX IDX_43C3D9C3CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27365BF48 FOREIGN KEY (sous_categorie_id) REFERENCES sous_categorie (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE sous_categorie ADD CONSTRAINT FK_52743D7BBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE sous_commande ADD CONSTRAINT FK_66A49228FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE sous_commande ADD CONSTRAINT FK_66A4922882EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE sous_commande ADD CONSTRAINT FK_66A49228A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE sous_commande ADD CONSTRAINT FK_66A49228D5E86FF FOREIGN KEY (etat_id) REFERENCES etat_commande (id)');
        $this->addSql('ALTER TABLE sous_commande ADD CONSTRAINT FK_66A49228F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3BE2F0A35 FOREIGN KEY (adresse_livraison_id) REFERENCES adresse_livraison (id)');
        $this->addSql('ALTER TABLE ville ADD CONSTRAINT FK_43C3D9C38D070D0B FOREIGN KEY (canton_id) REFERENCES canton (id)');
        $this->addSql('ALTER TABLE ville ADD CONSTRAINT FK_43C3D9C3CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE adresse_livraison ADD CONSTRAINT FK_B0B09C9A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE canton_departement ADD CONSTRAINT FK_C1B440128D070D0B FOREIGN KEY (canton_id) REFERENCES canton (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE canton_departement ADD CONSTRAINT FK_C1B44012CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DBE2F0A35 FOREIGN KEY (adresse_livraison_id) REFERENCES adresse_livraison (id)');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B6398260155 FOREIGN KEY (region_id) REFERENCES region (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FA4AEAFEA');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A4AEAFEA');
        $this->addSql('ALTER TABLE sous_commande DROP FOREIGN KEY FK_66A49228A4AEAFEA');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3A4AEAFEA');
        $this->addSql('ALTER TABLE sous_commande DROP FOREIGN KEY FK_66A49228D5E86FF');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FF347EFB');
        $this->addSql('ALTER TABLE sous_commande DROP FOREIGN KEY FK_66A49228F347EFB');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B6398260155');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27365BF48');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('ALTER TABLE sous_commande DROP FOREIGN KEY FK_66A49228FB88E14F');
        $this->addSql('ALTER TABLE adresse_livraison DROP FOREIGN KEY FK_B0B09C9A73F0036');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60A73F0036');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3A73F0036');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE etat_commande');
        $this->addSql('DROP TABLE fichier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE sous_categorie');
        $this->addSql('DROP TABLE sous_commande');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE ville');
        $this->addSql('ALTER TABLE canton_departement DROP FOREIGN KEY FK_C1B440128D070D0B');
        $this->addSql('ALTER TABLE canton_departement DROP FOREIGN KEY FK_C1B44012CCF9E01E');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DBE2F0A35');
    }
}
