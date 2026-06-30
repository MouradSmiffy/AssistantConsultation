<?php require_once 'includes_header.php'; ?>

<div class="topbar">
    <h2>Patients</h2>
    <a href="nouveau_patient.php" class="btn">➕ Nouveau patient</a>
</div>

<div class="card">
    <div class="search-box">
        <input type="text" id="recherche" placeholder="Rechercher un patient (nom, prénom)..." onkeyup="filtrer()">
    </div>
    <?php
    $stmt = $pdo->query("SELECT * FROM patients WHERE actif=1 ORDER BY nom, prenom");
    $patients = $stmt->fetchAll();
    ?>
    <?php if (empty($patients)): ?>
        <div class="empty-state">Aucun patient enregistré. <a href="nouveau_patient.php">Créer le premier dossier patient</a>.</div>
    <?php else: ?>
    <table id="tablePatients">
        <tr><th>Nom</th><th>Prénom</th><th>Âge</th><th>Sexe</th><th>Téléphone</th><th></th></tr>
        <?php foreach ($patients as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= htmlspecialchars($p['prenom']) ?></td>
            <td><?= calculer_age($p['date_naissance']) ?> ans</td>
            <td><?= $p['sexe'] ?></td>
            <td><?= htmlspecialchars($p['telephone'] ?? '—') ?></td>
            <td><a href="patient_detail.php?id=<?= $p['id'] ?>" class="btn btn-sm">Ouvrir le dossier</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

<script>
function filtrer() {
    const val = document.getElementById('recherche').value.toLowerCase();
    document.querySelectorAll('#tablePatients tr').forEach((row, i) => {
        if (i === 0) return;
        row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
}
</script>

<?php require_once 'includes_footer.php'; ?>
