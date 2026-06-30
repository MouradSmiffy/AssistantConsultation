<?php
function calculer_age($date_naissance) {
    $naissance = new DateTime($date_naissance);
    $aujourdhui = new DateTime();
    return $naissance->diff($aujourdhui)->y;
}

function calculer_imc($poids, $taille_cm) {
    if (!$poids || !$taille_cm) return null;
    $taille_m = $taille_cm / 100;
    return round($poids / ($taille_m * $taille_m), 1);
}

function interpreter_imc($imc) {
    if ($imc === null) return '';
    if ($imc < 16.5) return 'Dénutrition';
    if ($imc < 18.5) return 'Maigreur';
    if ($imc < 25) return 'Corpulence normale';
    if ($imc < 30) return 'Surpoids';
    if ($imc < 35) return 'Obésité modérée';
    if ($imc < 40) return 'Obésité sévère';
    return 'Obésité morbide';
}

// Estimation simplifiée du risque cardiovasculaire (style SCORE, à titre indicatif)
// Facteurs : âge, sexe, tabac, tension systolique, diabète, cholestérol total (optionnel)
function calculer_score_cardiovasculaire($age, $sexe, $tabac, $tas, $diabete, $cholesterol_total = null) {
    $points = 0;

    // Âge
    if ($age >= 65) $points += 4;
    elseif ($age >= 55) $points += 3;
    elseif ($age >= 45) $points += 2;
    elseif ($age >= 35) $points += 1;

    // Sexe (risque légèrement plus élevé chez l'homme)
    if ($sexe === 'M') $points += 1;

    // Tabac
    if ($tabac) $points += 2;

    // Tension artérielle systolique
    if ($tas >= 180) $points += 4;
    elseif ($tas >= 160) $points += 3;
    elseif ($tas >= 140) $points += 2;
    elseif ($tas >= 130) $points += 1;

    // Diabète
    if ($diabete) $points += 2;

    // Cholestérol total (mg/dL) si renseigné
    if ($cholesterol_total !== null && $cholesterol_total !== '') {
        if ($cholesterol_total >= 280) $points += 3;
        elseif ($cholesterol_total >= 240) $points += 2;
        elseif ($cholesterol_total >= 200) $points += 1;
    }

    if ($points <= 3) return ['niveau' => 'Faible', 'points' => $points, 'couleur' => 'success'];
    if ($points <= 7) return ['niveau' => 'Modéré', 'points' => $points, 'couleur' => 'warning'];
    if ($points <= 10) return ['niveau' => 'Élevé', 'points' => $points, 'couleur' => 'danger'];
    return ['niveau' => 'Très élevé', 'points' => $points, 'couleur' => 'danger'];
}

function rechercher_pistes_diagnostiques($pdo, $symptomes_saisis) {
    // Recherche simple par mots-clés dans la base de règles
    $stmt = $pdo->query("SELECT * FROM regles_diagnostic");
    $regles = $stmt->fetchAll();
    $resultats = [];
    $mots = preg_split('/[\s,;]+/', mb_strtolower($symptomes_saisis));
    $mots = array_filter($mots, fn($m) => mb_strlen($m) > 2);

    foreach ($regles as $regle) {
        $symptome_lower = mb_strtolower($regle['symptome']);
        $score = 0;
        foreach ($mots as $mot) {
            if (mb_strpos($symptome_lower, $mot) !== false) $score++;
        }
        if ($score > 0) {
            $regle['score'] = $score;
            $resultats[] = $regle;
        }
    }
    usort($resultats, fn($a, $b) => $b['score'] <=> $a['score']);
    return array_slice($resultats, 0, 5);
}

function badge_urgence($niveau) {
    $map = ['faible' => 'success', 'moyen' => 'warning', 'eleve' => 'danger'];
    return $map[$niveau] ?? 'success';
}
