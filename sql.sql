-- ============================================================
-- BASE DE DONNÉES : GESTION RECETTES & DÉPENSES TRANSPORT
-- MySQL 8+
-- Sans clés étrangères - Intégrité gérée par l'application
-- ============================================================

CREATE DATABASE IF NOT EXISTS gestion_camion
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_camion;

-- ============================================================
-- 1. TRAVAILLEURS
-- ============================================================
CREATE TABLE travailleurs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(50) NOT NULL UNIQUE COMMENT 'Format: MWTR-2026-0001',
    nom VARCHAR(100) NOT NULL,
    postnom VARCHAR(100) NULL,
    prenom VARCHAR(100) NULL,
    sexe ENUM('M', 'F') NULL,
    telephone VARCHAR(30) NULL,
    email VARCHAR(150) NULL UNIQUE,
    motde_passe VARCHAR(255) DEFAULT '1234',
    adresse VARCHAR(255) NULL,
    date_embauche DATE NULL,
    profil VARCHAR(255) NOT NULL DEFAULT '',
    statut ENUM('ACTIF', 'SUPPRIME', 'SUSPENDU', 'LICENCIE') NOT NULL DEFAULT 'ACTIF',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. FONCTIONS DES TRAVAILLEURS
-- ============================================================
CREATE TABLE fonctions_travailleur (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE COMMENT 'chauffeur, convoyeur, mecanicien, aide-chauffeur, etc.',
    statut INT DEFAULT 1 COMMENT '0: supprimé, 1: actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 3. ATTRIBUTION DES FONCTIONS AUX TRAVAILLEURS
-- ============================================================
CREATE TABLE attribution_fonctions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    travailleur_id INT UNSIGNED NOT NULL COMMENT 'Référence vers travailleurs.id',
    fonction_id INT UNSIGNED NOT NULL COMMENT 'Référence vers fonctions_travailleur.id',
    taux_remuneration DECIMAL(15,2) NULL COMMENT 'Taux de rémunération pour cette fonction',
    statut ENUM('ACTIF', 'SUSPENDU') NOT NULL DEFAULT 'ACTIF',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 4. VÉHICULES
-- ============================================================
CREATE TABLE vehicules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) NOT NULL UNIQUE,
    numero_chassis VARCHAR(100) NULL UNIQUE,
    marque VARCHAR(100) NULL,
    modele VARCHAR(100) NULL,
    annee YEAR NULL,
    couleur VARCHAR(50) NULL,
    type_vehicule VARCHAR(100) NULL,
    capacite_passagers INT UNSIGNED NULL,
    kilometrage_initial DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    date_acquisition DATE NULL,
    statut ENUM('ACTIF', 'EN_MAINTENANCE', 'HORS_SERVICE', 'VENDU') NOT NULL DEFAULT 'ACTIF',
    statut_supprime INT DEFAULT 0 COMMENT '0: actif, 1: supprimé',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 5. AFFECTATIONS DES TRAVAILLEURS AUX VÉHICULES
-- ============================================================
CREATE TABLE affectations_vehicule (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicule_id INT UNSIGNED NOT NULL COMMENT 'Référence vers vehicules.id',
    attribution_id INT UNSIGNED NOT NULL COMMENT 'Référence vers attribution_fonctions.id',
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    observation TEXT NULL,
    statut_supprime INT DEFAULT 0 COMMENT '0: actif, 1: supprimé',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 6. CATÉGORIES DES DÉPENSES
-- ============================================================
CREATE TABLE categories_depenses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    type_depense ENUM('participatif', 'non_participatif') NOT NULL DEFAULT 'non_participatif' COMMENT 'participatif: affecte la rémunération, non_participatif: ne l''affecte pas',
    actif TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0: inactif, 1: actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 7. REMUNERATIONS DES TRAVAILLEURS
-- ============================================================
CREATE TABLE remunerations_travailleur (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attribution_id INT UNSIGNED NOT NULL COMMENT 'Référence vers attribution_fonctions.id',
    affectation_vehicule_id INT UNSIGNED NOT NULL COMMENT 'Référence vers affectations_vehicule.id',
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0: inactif, 1: actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 8. UTILISATEURS
-- ============================================================
CREATE TABLE utilisateurs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(100) NOT NULL,
    matricule VARCHAR(100) NOT NULL UNIQUE COMMENT 'Format automatique: ADMIN-001/2026',
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'CEO', 'COMPTABLE') NOT NULL DEFAULT 'ADMIN',
    profil VARCHAR(255) NOT NULL DEFAULT '',
    statut INT DEFAULT 0 COMMENT '0: actif, 1: supprimé',
    derniere_connexion DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 9. RECETTES
-- ============================================================
CREATE TABLE recettes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affection_vehicule INT UNSIGNED NOT NULL COMMENT 'Référence vers affectations_vehicule.id',
    date_recette DATE NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    devise ENUM('USD', 'CDF') NOT NULL DEFAULT 'USD',
    description TEXT NULL,
    cloturer INT DEFAULT 0 COMMENT '0: non-clôturée, 1: clôturée/rémunérée',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_recettes_montant CHECK (montant > 0)
) ENGINE=InnoDB;

-- ============================================================
-- 10. DÉPENSES
-- ============================================================
CREATE TABLE depenses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affection_vehicule INT UNSIGNED NOT NULL COMMENT 'Référence vers affectations_vehicule.id',
    categorie_depense_id INT UNSIGNED NOT NULL COMMENT 'Référence vers categories_depenses.id',
    date_depense DATE NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    devise ENUM('USD', 'CDF') NOT NULL DEFAULT 'USD',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_depenses_montant CHECK (montant > 0)
) ENGINE=InnoDB;

-- ============================================================
-- 11. MOUVEMENTS DE CAISSE (Suivi détaillé des entrées/sorties)
-- ============================================================
CREATE TABLE mouvements_caisse (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_mouvement ENUM('ENTREE', 'SORTIE') NOT NULL,
    recette_id INT UNSIGNED NULL COMMENT 'Référence vers recettes.id si ENTREE',
    depense_id INT UNSIGNED NULL COMMENT 'Référence vers depenses.id si SORTIE',
    montant DECIMAL(15,2) NOT NULL,
    solde_avant DECIMAL(15,2) NOT NULL,
    solde_apres DECIMAL(15,2) NOT NULL,
    description TEXT NULL,
    cree_par INT UNSIGNED NOT NULL COMMENT 'Référence vers utilisateurs.id',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_mouvements_montant CHECK (montant > 0),
    CONSTRAINT chk_mouvements_coherence CHECK (
        (type_mouvement = 'ENTREE' AND recette_id IS NOT NULL AND depense_id IS NULL) OR
        (type_mouvement = 'SORTIE' AND depense_id IS NOT NULL AND recette_id IS NULL)
    )
) ENGINE=InnoDB;

-- ============================================================
-- 12. JOURNAL D'AUDIT
-- ============================================================
CREATE TABLE journal_audit (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT UNSIGNED NULL COMMENT 'Référence vers utilisateurs.id',
    action VARCHAR(50) NOT NULL COMMENT 'CREATE, UPDATE, DELETE, LOGIN, LOGOUT',
    table_concernee VARCHAR(100) NOT NULL,
    enregistrement_id INT UNSIGNED NULL,
    ancienne_valeur JSON NULL,
    nouvelle_valeur JSON NULL,
    adresse_ip VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DONNÉES DE BASE
-- ============================================================

-- Fonctions par défaut
INSERT INTO fonctions_travailleur (nom, statut) VALUES
('Chauffeur', 1),
('Convoyeur', 1),
('Aide-chauffeur', 1),
('Mécanicien', 1),
('Chef de garage', 1);

-- Catégories de dépenses par défaut
INSERT INTO categories_depenses (nom, type_depense, actif) VALUES
('Carburant', 'non_participatif', 1),
('Péage', 'non_participatif', 1),
('Réparation', 'participatif', 1),
('Maintenance', 'participatif', 1),
('Pièces de rechange', 'participatif', 1),
('Lavage', 'non_participatif', 1),
('Amende', 'participatif', 1),
('Autre', 'non_participatif', 1);

-- Utilisateur admin par défaut (mot de passe: admin123 - À CHANGER !)
INSERT INTO utilisateurs (matricule, mot_de_passe, role, statut) VALUES
('ADMIN-001', '$2y$10$YourHashedPasswordHere', 'ADMIN', 0);
