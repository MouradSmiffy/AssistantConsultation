<?php
require_once __DIR__ . '/../app/includes/db.php';
require_once __DIR__ . '/../app/includes/fonctions.php';
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assistant de Consultation</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
    <div class="sidebar">
        <h1>Assistant Consultation<span>Médecine Générale</span></h1>
        <nav>
            <a href="index.php" class="<?= $page=='index'?'active':'' ?>">🏠 Accueil</a>
            <a href="patients.php" class="<?= $page=='patients'?'active':'' ?>">👥 Patients</a>
            <a href="nouveau_patient.php" class="<?= $page=='nouveau_patient'?'active':'' ?>">➕ Nouveau patient</a>
            <a href="vaccinations.php" class="<?= $page=='vaccinations'?'active':'' ?>">💉 Vaccinations</a>
            <a href="chroniques.php" class="<?= $page=='chroniques'?'active':'' ?>">📈 Maladies chroniques</a>
            <a href="parametres.php" class="<?= $page=='parametres'?'active':'' ?>">⚙️ Paramètres</a>
        </nav>
    </div>
    <div class="main">
