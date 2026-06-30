-- Assistant de Consultation - Schéma de base de données
CREATE DATABASE IF NOT EXISTS assistant_consultation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE assistant_consultation;

-- Patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    sexe ENUM('M','F') NOT NULL,
    telephone VARCHAR(30),
    adresse VARCHAR(255),
    groupe_sanguin VARCHAR(5),
    allergies TEXT,
    antecedents_medicaux TEXT,
    antecedents_chirurgicaux TEXT,
    antecedents_familiaux TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actif TINYINT(1) DEFAULT 1
);

-- Consultations
CREATE TABLE consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    date_consultation DATETIME DEFAULT CURRENT_TIMESTAMP,
    motif TEXT,
    poids DECIMAL(5,2),
    taille DECIMAL(5,2),
    imc DECIMAL(5,2),
    tension_systolique INT,
    tension_diastolique INT,
    frequence_cardiaque INT,
    temperature DECIMAL(4,2),
    symptomes TEXT,
    examen_clinique TEXT,
    diagnostic TEXT,
    pistes_diagnostiques TEXT,
    traitement TEXT,
    notes TEXT,
    score_cardiovasculaire VARCHAR(50),
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Ordonnances
CREATE TABLE ordonnances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    patient_id INT NOT NULL,
    date_ordonnance DATETIME DEFAULT CURRENT_TIMESTAMP,
    contenu TEXT NOT NULL, -- JSON: liste des médicaments (nom, dosage, posologie, durée)
    FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Certificats médicaux
CREATE TABLE certificats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    consultation_id INT,
    type_certificat VARCHAR(100) NOT NULL, -- repos, aptitude, sport, scolaire, etc.
    contenu TEXT NOT NULL,
    date_debut DATE,
    date_fin DATE,
    date_emission DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Vaccinations
CREATE TABLE vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    nom_vaccin VARCHAR(150) NOT NULL,
    date_administration DATE,
    date_rappel_prevue DATE,
    statut ENUM('fait','a_prevoir','en_retard') DEFAULT 'a_prevoir',
    notes VARCHAR(255),
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Suivi des maladies chroniques
CREATE TABLE maladies_chroniques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    type_maladie ENUM('HTA','Diabete','Asthme','Autre') NOT NULL,
    date_diagnostic DATE,
    traitement_actuel TEXT,
    notes TEXT,
    actif TINYINT(1) DEFAULT 1,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Suivi des paramètres pour maladies chroniques (mesures dans le temps)
CREATE TABLE suivi_parametres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maladie_id INT NOT NULL,
    date_mesure DATE DEFAULT (CURRENT_DATE),
    parametre VARCHAR(50) NOT NULL, -- ex: glycemie, tension, debit_expiratoire
    valeur VARCHAR(50) NOT NULL,
    unite VARCHAR(20),
    notes VARCHAR(255),
    FOREIGN KEY (maladie_id) REFERENCES maladies_chroniques(id) ON DELETE CASCADE
);

-- Base de règles d'aide au diagnostic (symptômes -> pistes)
CREATE TABLE regles_diagnostic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    symptome VARCHAR(150) NOT NULL,
    piste_diagnostique VARCHAR(255) NOT NULL,
    niveau_urgence ENUM('faible','moyen','eleve') DEFAULT 'faible',
    conseil VARCHAR(255)
);

-- Modèles de vaccins standards (calendrier)
CREATE TABLE vaccins_calendrier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_vaccin VARCHAR(150) NOT NULL,
    age_recommande_mois INT,
    rappel_annees INT
);

-- Paramètres du cabinet (info médecin pour ordonnances/certificats)
CREATE TABLE parametres_cabinet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_medecin VARCHAR(150),
    specialite VARCHAR(100) DEFAULT 'Médecine Générale',
    adresse_cabinet VARCHAR(255),
    telephone_cabinet VARCHAR(30),
    numero_ordre VARCHAR(50),
    logo_path VARCHAR(255)
);

INSERT INTO parametres_cabinet (nom_medecin, specialite) VALUES ('Dr. ', 'Médecine Générale');
