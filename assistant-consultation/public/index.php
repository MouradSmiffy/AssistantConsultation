<?php require_once 'includes_header.php'; ?>

<div class="topbar">
    <h2>Tableau de bord</h2>
</div>

<?php
$nb_patients = $pdo->query("SELECT COUNT(*) c FROM patients WHERE actif=1")->fetch()['c'];
$nb_consultations_mois = $pdo->query("SELECT COUNT(*) c FROM consultations WHERE MONTH(date_consultation)=MONTH(CURDATE()) AND YEAR(date_consultation)=YEAR(CURDATE())")->fetch()['c'];
$nb_vaccins_retard = $pdo->query("SELECT COUNT(*) c FROM vaccinations WHERE statut='en_retard' OR (statut='a_prevoir' AND date_rappel_prevue < CURDATE())")->fetch()['c'];
$nb_chroniques = $pdo->query("SELECT COUNT(*) c FROM maladies_chroniques WHERE actif=1")->fetch()['c'];
?>

<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-box"><div class="value"><?= $nb_patients ?></div><div class="label">Patients suivis</div></div>
    <div class="stat-box"><div class="value"><?= $nb_consultations_mois ?></div><div class="label">Consultations ce mois</div></div>
    <div class="stat-box"><div class="value"><?= $nb_vaccins_retard ?></div><div class="label">Rappels vaccinaux en retard</div></div>
    <div class="stat-box"><div class="value"><?= $nb_chroniques ?></div><div class="label">Suivis maladies chroniques</div></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h3>Dernières consultations</h3>
        <?php
        $stmt = $pdo->query("SELECT c.*, p.nom, p.prenom FROM consultations c JOIN patients p ON c.patient_id=p.id ORDER BY c.date_consultation DESC LIMIT 6");
        $consults = $stmt->fetchAll();
        ?>
        <?php if (empty($consults)): ?>
            <div class="empty-state">Aucune consultation enregistrée pour le moment.</div>
        <?php else: ?>
        <table>
            <tr><th>Patient</th><th>Date</th><th>Motif</th></tr>
            <?php foreach ($consults as $c): ?>
            <tr>
                <td><a href="patient_detail.php?id=<?= $c['patient_id'] ?>"><?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></a></td>
                <td><?= date('d/m/Y', strtotime($c['date_consultation'])) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($c['motif'] ?? '', 0, 40, '...')) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Rappels de vaccination à venir</h3>
        <?php
        $stmt = $pdo->query("SELECT v.*, p.nom, p.prenom FROM vaccinations v JOIN patients p ON v.patient_id=p.id WHERE v.statut != 'fait' ORDER BY v.date_rappel_prevue ASC LIMIT 6");
        $vaccins = $stmt->fetchAll();
        ?>
        <?php if (empty($vaccins)): ?>
            <div class="empty-state">Aucun rappel en attente.</div>
        <?php else: ?>
        <table>
            <tr><th>Patient</th><th>Vaccin</th><th>Échéance</th></tr>
            <?php foreach ($vaccins as $v):
                $retard = $v['date_rappel_prevue'] && strtotime($v['date_rappel_prevue']) < time();
            ?>
            <tr>
                <td><a href="patient_detail.php?id=<?= $v['patient_id'] ?>"><?= htmlspecialchars($v['prenom'].' '.$v['nom']) ?></a></td>
                <td><?= htmlspecialchars($v['nom_vaccin']) ?></td>
                <td><span class="badge badge-<?= $retard ? 'danger' : 'warning' ?>"><?= $v['date_rappel_prevue'] ? date('d/m/Y', strtotime($v['date_rappel_prevue'])) : '—' ?></span></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes_footer.php'; ?>
