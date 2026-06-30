<?php require_once 'includes_header.php';
$patient_id = (int)($_GET['patient_id'] ?? $_POST['patient_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id=?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();
if (!$patient) { echo "<div class='alert alert-warning'>Patient introuvable.</div>"; require 'includes_footer.php'; exit; }
$age = calculer_age($patient['date_naissance']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer'])) {
    $imc = calculer_imc((float)$_POST['poids'], (float)$_POST['taille']);
    $score = null;
    if (!empty($_POST['tension_systolique'])) {
        $r = calculer_score_cardiovasculaire($age, $patient['sexe'], !empty($_POST['tabac']), (int)$_POST['tension_systolique'], !empty($_POST['diabete']), $_POST['cholesterol'] ?: null);
        $score = $r['niveau'].' ('.$r['points'].' pts)';
    }
    $stmt = $pdo->prepare("INSERT INTO consultations (patient_id, motif, poids, taille, imc, tension_systolique, tension_diastolique, frequence_cardiaque, temperature, symptomes, examen_clinique, diagnostic, pistes_diagnostiques, traitement, notes, score_cardiovasculaire) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $patient_id, $_POST['motif'], $_POST['poids'] ?: null, $_POST['taille'] ?: null, $imc,
        $_POST['tension_systolique'] ?: null, $_POST['tension_diastolique'] ?: null, $_POST['frequence_cardiaque'] ?: null,
        $_POST['temperature'] ?: null, $_POST['symptomes'], $_POST['examen_clinique'], $_POST['diagnostic'],
        $_POST['pistes_diagnostiques'] ?? '', $_POST['traitement'], $_POST['notes'], $score
    ]);
    $consultation_id = $pdo->lastInsertId();

    if (isset($_POST['action_suivante']) && $_POST['action_suivante'] === 'ordonnance') {
        header("Location: ordonnance.php?consultation_id=$consultation_id");
    } elseif (isset($_POST['action_suivante']) && $_POST['action_suivante'] === 'certificat') {
        header("Location: certificat.php?consultation_id=$consultation_id");
    } else {
        header("Location: consultation_detail.php?id=$consultation_id");
    }
    exit;
}

// Recherche de pistes diagnostiques (appel AJAX simplifié via GET)
if (isset($_GET['ajax_symptomes'])) {
    $pistes = rechercher_pistes_diagnostiques($pdo, $_GET['ajax_symptomes']);
    header('Content-Type: application/json');
    echo json_encode($pistes);
    exit;
}
?>

<div class="topbar"><h2>Nouvelle consultation — <?= htmlspecialchars($patient['prenom'].' '.$patient['nom']) ?></h2></div>

<form method="POST" id="formConsultation">
<input type="hidden" name="patient_id" value="<?= $patient_id ?>">

<div class="card">
    <h3>Motif de consultation</h3>
    <div class="form-group"><textarea name="motif" rows="2" placeholder="Raison de la visite..."></textarea></div>
</div>

<div class="card">
    <h3>Constantes & calculs automatiques</h3>
    <div class="grid grid-4">
        <div class="form-group"><label>Poids (kg)</label><input type="number" step="0.1" name="poids" id="poids" oninput="majIMC()"></div>
        <div class="form-group"><label>Taille (cm)</label><input type="number" step="0.1" name="taille" id="taille" oninput="majIMC()"></div>
        <div class="form-group"><label>Tension systolique</label><input type="number" name="tension_systolique" id="tas" oninput="majScoreCV()"></div>
        <div class="form-group"><label>Tension diastolique</label><input type="number" name="tension_diastolique"></div>
        <div class="form-group"><label>Fréquence cardiaque</label><input type="number" name="frequence_cardiaque"></div>
        <div class="form-group"><label>Température (°C)</label><input type="number" step="0.1" name="temperature"></div>
        <div class="form-group"><label><input type="checkbox" name="tabac" id="tabac" style="width:auto;display:inline-block;" onchange="majScoreCV()"> Fumeur</label></div>
        <div class="form-group"><label><input type="checkbox" name="diabete" id="diabete" style="width:auto;display:inline-block;" onchange="majScoreCV()"> Diabétique</label></div>
        <div class="form-group"><label>Cholestérol total (mg/dL, optionnel)</label><input type="number" name="cholesterol" id="cholesterol" oninput="majScoreCV()"></div>
    </div>

    <div class="grid grid-2" style="margin-top:10px;">
        <div class="stat-box"><div class="value" id="resultatIMC">—</div><div class="label" id="labelIMC">IMC</div></div>
        <div class="stat-box"><div class="value" id="resultatCV">—</div><div class="label">Risque cardiovasculaire estimé</div></div>
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin-top:8px;">⚠️ Estimation indicative simplifiée, ne remplace pas un score validé (ex: SCORE2). À interpréter par le médecin.</p>
</div>

<div class="card">
    <h3>Symptômes & aide au diagnostic</h3>
    <div class="form-group">
        <label>Symptômes observés</label>
        <textarea name="symptomes" id="symptomes" rows="3" placeholder="Ex: fièvre, toux, douleur abdominale..." oninput="chercherPistes()"></textarea>
    </div>
    <div id="zonePistes"></div>
    <div class="form-group">
        <label>Pistes diagnostiques retenues (modifiable)</label>
        <textarea name="pistes_diagnostiques" id="pistes_diagnostiques" rows="2"></textarea>
    </div>
</div>

<div class="card">
    <h3>Examen clinique & conclusion</h3>
    <div class="form-group"><label>Examen clinique</label><textarea name="examen_clinique" rows="2"></textarea></div>
    <div class="form-group"><label>Diagnostic retenu</label><textarea name="diagnostic" rows="2"></textarea></div>
    <div class="form-group"><label>Traitement / conduite à tenir</label><textarea name="traitement" rows="2"></textarea></div>
    <div class="form-group"><label>Notes complémentaires</label><textarea name="notes" rows="2"></textarea></div>
</div>

<div class="card no-print">
    <button type="submit" name="enregistrer" value="1" class="btn">💾 Enregistrer la consultation</button>
    <button type="submit" name="enregistrer" value="1" onclick="document.getElementById('action_suivante').value='ordonnance'" class="btn btn-secondary">+ Générer une ordonnance</button>
    <button type="submit" name="enregistrer" value="1" onclick="document.getElementById('action_suivante').value='certificat'" class="btn btn-secondary">+ Générer un certificat</button>
    <input type="hidden" name="action_suivante" id="action_suivante" value="">
</div>
</form>

<script>
function majIMC() {
    const poids = parseFloat(document.getElementById('poids').value);
    const taille = parseFloat(document.getElementById('taille').value);
    const box = document.getElementById('resultatIMC');
    const label = document.getElementById('labelIMC');
    if (poids > 0 && taille > 0) {
        const t = taille / 100;
        const imc = (poids / (t*t)).toFixed(1);
        box.innerText = imc;
        const interp = interpreterIMC(parseFloat(imc));
        label.innerText = 'IMC — ' + interp;
    } else {
        box.innerText = '—'; label.innerText = 'IMC';
    }
}
function interpreterIMC(imc) {
    if (imc < 16.5) return 'Dénutrition';
    if (imc < 18.5) return 'Maigreur';
    if (imc < 25) return 'Corpulence normale';
    if (imc < 30) return 'Surpoids';
    if (imc < 35) return 'Obésité modérée';
    if (imc < 40) return 'Obésité sévère';
    return 'Obésité morbide';
}

const AGE = <?= $age ?>;
const SEXE = "<?= $patient['sexe'] ?>";

function majScoreCV() {
    const tas = parseInt(document.getElementById('tas').value) || 0;
    const tabac = document.getElementById('tabac').checked;
    const diabete = document.getElementById('diabete').checked;
    const chol = parseInt(document.getElementById('cholesterol').value) || 0;
    let points = 0;
    if (AGE >= 65) points += 4; else if (AGE >= 55) points += 3; else if (AGE >= 45) points += 2; else if (AGE >= 35) points += 1;
    if (SEXE === 'M') points += 1;
    if (tabac) points += 2;
    if (tas >= 180) points += 4; else if (tas >= 160) points += 3; else if (tas >= 140) points += 2; else if (tas >= 130) points += 1;
    if (diabete) points += 2;
    if (chol >= 280) points += 3; else if (chol >= 240) points += 2; else if (chol >= 200) points += 1;

    let niveau = 'Faible';
    if (points > 10) niveau = 'Très élevé';
    else if (points > 7) niveau = 'Élevé';
    else if (points > 3) niveau = 'Modéré';

    document.getElementById('resultatCV').innerText = niveau + ' (' + points + ' pts)';
}

let timeoutId;
function chercherPistes() {
    clearTimeout(timeoutId);
    const val = document.getElementById('symptomes').value;
    if (val.length < 3) { document.getElementById('zonePistes').innerHTML = ''; return; }
    timeoutId = setTimeout(() => {
        fetch('nouvelle_consultation.php?ajax_symptomes=' + encodeURIComponent(val))
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) { document.getElementById('zonePistes').innerHTML = ''; return; }
                let html = '<div class="alert alert-info"><strong>Pistes possibles (à valider par le médecin) :</strong><ul style="margin:8px 0 0 0;">';
                data.forEach(d => {
                    const badgeClass = d.niveau_urgence === 'eleve' ? 'danger' : (d.niveau_urgence === 'moyen' ? 'warning' : 'success');
                    html += `<li style="margin-bottom:6px;"><span class="badge badge-${badgeClass}">${d.niveau_urgence}</span> ${d.piste_diagnostique} <br><span style="font-size:12px;color:var(--text-muted);">${d.conseil || ''}</span> — <a href="#" onclick="ajouterPiste('${d.piste_diagnostique.replace(/'/g,"\\'")}'); return false;">Ajouter</a></li>`;
                });
                html += '</ul></div>';
                document.getElementById('zonePistes').innerHTML = html;
            });
    }, 400);
}
function ajouterPiste(texte) {
    const champ = document.getElementById('pistes_diagnostiques');
    champ.value = champ.value ? champ.value + '\n' + texte : texte;
}
</script>

<?php require_once 'includes_footer.php'; ?>
