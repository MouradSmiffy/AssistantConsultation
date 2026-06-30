<?php require_once 'includes_header.php';
$patient_id = (int)($_GET['patient_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO vaccinations (patient_id, nom_vaccin, date_administration, date_rappel_prevue, statut, notes) VALUES (?,?,?,?,?,?)");
    $stmt->execute([
        $_POST['patient_id'], $_POST['nom_vaccin'], $_POST['date_administration'] ?: null,
        $_POST['date_rappel_prevue'] ?: null, $_POST['statut'], $_POST['notes']
    ]);
    header("Location: vaccinations.php?patient_id=" . $_POST['patient_id']);
    exit;
}

if (isset($_GET['marquer_fait'])) {
    $stmt = $pdo->prepare("UPDATE vaccinations SET statut='fait', date_administration=CURDATE() WHERE id=?");
    $stmt->execute([$_GET['marquer_fait']]);
    header("Location: vaccinations.php?patient_id=" . $patient_id);
    exit;
}
?>

<div class="topbar"><h2>Vaccinations</h2></div>

<?php if ($patient_id):
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id=?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch();
?>
<div class="card">
    <h3>Ajouter un vaccin pour <?= htmlspecialchars($patient['prenom'].' '.$patient['nom']) ?></h3>
    <form method="POST">
        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
        <div class="grid grid-4">
            <div class="form-group">
                <label>Vaccin</label>
                <select name="nom_vaccin">
                    <?php $vaccins = $pdo->query("SELECT DISTINCT nom_vaccin FROM vaccins_calendrier")->fetchAll(); ?>
                    <?php foreach ($vaccins as $v): ?>
                        <option value="<?= htmlspecialchars($v['nom_vaccin']) ?>"><?= htmlspecialchars($v['nom_vaccin']) ?></option>
                    <?php endforeach; ?>
                    <option value="Autre">Autre (préciser dans les notes)</option>
                </select>
            </div>
            <div class="form-group"><label>Date d'administration</label><input type="date" name="date_administration"></div>
            <div class="form-group"><label>Date du prochain rappel</label><input type="date" name="date_rappel_prevue"></div>
            <div class="form-group"><label>Statut</label>
                <select name="statut"><option value="fait">Fait</option><option value="a_prevoir">À prévoir</option><option value="en_retard">En retard</option></select>
            </div>
        </div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes"></div>
        <button type="submit" class="btn">Ajouter</button>
        <a href="patient_detail.php?id=<?= $patient_id ?>" class="btn btn-secondary">← Retour au dossier</a>
    </form>
</div>

<div class="card">
    <h3>Historique vaccinal</h3>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM vaccinations WHERE patient_id=? ORDER BY date_rappel_prevue ASC");
    $stmt->execute([$patient_id]);
    $list = $stmt->fetchAll();
    ?>
    <?php if (empty($list)): ?>
        <div class="empty-state">Aucune vaccination enregistrée.</div>
    <?php else: ?>
    <table>
        <tr><th>Vaccin</th><th>Date administration</th><th>Prochain rappel</th><th>Statut</th><th></th></tr>
        <?php foreach ($list as $v): ?>
        <tr>
            <td><?= htmlspecialchars($v['nom_vaccin']) ?></td>
            <td><?= $v['date_administration'] ? date('d/m/Y', strtotime($v['date_administration'])) : '—' ?></td>
            <td><?= $v['date_rappel_prevue'] ? date('d/m/Y', strtotime($v['date_rappel_prevue'])) : '—' ?></td>
            <td><span class="badge badge-<?= $v['statut']=='fait'?'success':($v['statut']=='en_retard'?'danger':'warning') ?>"><?= str_replace('_',' ',$v['statut']) ?></span></td>
            <td><?php if ($v['statut'] !== 'fait'): ?><a href="?marquer_fait=<?= $v['id'] ?>&patient_id=<?= $patient_id ?>" class="btn btn-sm btn-secondary">Marquer fait</a><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
    <h3>Tous les rappels en attente</h3>
    <?php
    $list = $pdo->query("SELECT v.*, p.nom, p.prenom FROM vaccinations v JOIN patients p ON v.patient_id=p.id WHERE v.statut != 'fait' ORDER BY v.date_rappel_prevue ASC")->fetchAll();
    ?>
    <?php if (empty($list)): ?>
        <div class="empty-state">Aucun rappel en attente. Ouvrez un dossier patient pour ajouter une vaccination.</div>
    <?php else: ?>
    <table>
        <tr><th>Patient</th><th>Vaccin</th><th>Échéance</th><th>Statut</th></tr>
        <?php foreach ($list as $v): ?>
        <tr>
            <td><a href="patient_detail.php?id=<?= $v['patient_id'] ?>"><?= htmlspecialchars($v['prenom'].' '.$v['nom']) ?></a></td>
            <td><?= htmlspecialchars($v['nom_vaccin']) ?></td>
            <td><?= $v['date_rappel_prevue'] ? date('d/m/Y', strtotime($v['date_rappel_prevue'])) : '—' ?></td>
            <td><span class="badge badge-<?= $v['statut']=='en_retard'?'danger':'warning' ?>"><?= str_replace('_',' ',$v['statut']) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'includes_footer.php'; ?>
