<?php require_once 'includes_header.php'; ?>

<div class="topbar"><h2>Nouveau dossier patient</h2></div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO patients (nom, prenom, date_naissance, sexe, telephone, adresse, groupe_sanguin, allergies, antecedents_medicaux, antecedents_chirurgicaux, antecedents_familiaux) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], $_POST['sexe'],
        $_POST['telephone'], $_POST['adresse'], $_POST['groupe_sanguin'],
        $_POST['allergies'], $_POST['antecedents_medicaux'], $_POST['antecedents_chirurgicaux'], $_POST['antecedents_familiaux']
    ]);
    $id = $pdo->lastInsertId();
    header("Location: patient_detail.php?id=$id&nouveau=1");
    exit;
}
?>

<form method="POST" class="card">
    <h3>Informations générales</h3>
    <div class="grid grid-2">
        <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
        <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" required></div>
        <div class="form-group"><label>Date de naissance *</label><input type="date" name="date_naissance" required></div>
        <div class="form-group"><label>Sexe *</label>
            <select name="sexe" required><option value="F">Féminin</option><option value="M">Masculin</option></select>
        </div>
        <div class="form-group"><label>Téléphone</label><input type="text" name="telephone"></div>
        <div class="form-group"><label>Groupe sanguin</label>
            <select name="groupe_sanguin">
                <option value="">Inconnu</option>
                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gs): ?>
                <option value="<?= $gs ?>"><?= $gs ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group"><label>Adresse</label><input type="text" name="adresse"></div>

    <h3>Antécédents médicaux</h3>
    <div class="form-group"><label>Allergies connues</label><textarea name="allergies" rows="2" placeholder="Ex: Pénicilline, arachides..."></textarea></div>
    <div class="form-group"><label>Antécédents médicaux</label><textarea name="antecedents_medicaux" rows="2"></textarea></div>
    <div class="form-group"><label>Antécédents chirurgicaux</label><textarea name="antecedents_chirurgicaux" rows="2"></textarea></div>
    <div class="form-group"><label>Antécédents familiaux</label><textarea name="antecedents_familiaux" rows="2"></textarea></div>

    <button type="submit" class="btn">Créer le dossier</button>
    <a href="patients.php" class="btn btn-secondary">Annuler</a>
</form>

<?php require_once 'includes_footer.php'; ?>
