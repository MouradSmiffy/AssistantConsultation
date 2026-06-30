USE assistant_consultation;

-- Règles d'aide au diagnostic (base simple, à titre indicatif uniquement)
INSERT INTO regles_diagnostic (symptome, piste_diagnostique, niveau_urgence, conseil) VALUES
('Fièvre + toux', 'Infection respiratoire (grippe, bronchite, pneumonie à exclure)', 'moyen', 'Ausculter, vérifier saturation si possible'),
('Fièvre + douleur abdominale', 'Gastro-entérite, appendicite à exclure', 'eleve', 'Examiner abdomen, signes de défense'),
('Céphalées + fièvre + raideur nuque', 'Méningite à exclure en urgence', 'eleve', 'Orienter en urgence si suspicion'),
('Douleur thoracique', 'Cause cardiaque à exclure en priorité (SCA, angor)', 'eleve', 'ECG si disponible, orienter si doute'),
('Toux chronique + essoufflement', 'Asthme, BPCO, insuffisance cardiaque', 'moyen', 'Explorer antécédents tabagiques et allergiques'),
('Polyurie + polydipsie + amaigrissement', 'Diabète à rechercher', 'moyen', 'Glycémie capillaire'),
('Céphalées + vertiges + tension élevée', 'HTA mal contrôlée', 'moyen', 'Vérifier tension à plusieurs reprises'),
('Douleur articulaire + gonflement', 'Arthrite, goutte, infection articulaire', 'faible', 'Préciser localisation et durée'),
('Éruption cutanée + démangeaisons', 'Allergie, dermatite, infection cutanée', 'faible', 'Identifier facteur déclenchant'),
('Diarrhée + vomissements', 'Gastro-entérite aiguë', 'faible', 'Surveiller déshydratation surtout chez enfant/personne âgée'),
('Toux + perte de poids + sueurs nocturnes', 'Tuberculose à évoquer', 'moyen', 'Bilan complémentaire recommandé'),
('Douleur lombaire', 'Lombalgie commune, lithiase rénale à exclure', 'faible', 'Préciser irradiation et signes urinaires'),
('Fatigue chronique + pâleur', 'Anémie à rechercher', 'faible', 'NFS recommandée'),
('Palpitations + anxiété', 'Trouble anxieux, dysthyroïdie, arythmie', 'moyen', 'ECG et bilan thyroïdien si persistant'),
('Mal de gorge + fièvre', 'Angine (virale ou streptococcique)', 'faible', 'Test rapide streptocoque si disponible');

-- Calendrier vaccinal standard simplifié
INSERT INTO vaccins_calendrier (nom_vaccin, age_recommande_mois, rappel_annees) VALUES
('BCG', 0, NULL),
('Hépatite B (1ère dose)', 0, NULL),
('Polio (1ère dose)', 2, NULL),
('DTC (Diphtérie-Tétanos-Coqueluche) 1ère dose', 2, NULL),
('Polio (2ème dose)', 4, NULL),
('DTC 2ème dose', 4, NULL),
('Polio (3ème dose)', 6, NULL),
('DTC 3ème dose', 6, NULL),
('Rougeole-Oreillons-Rubéole (1ère dose)', 9, NULL),
('Rougeole-Oreillons-Rubéole (rappel)', 18, NULL),
('DTC rappel', 72, 10),
('Tétanos adulte', NULL, 10),
('Grippe saisonnière', NULL, 1),
('COVID-19 rappel', NULL, 1);
