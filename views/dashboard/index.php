<?php
$matricule = $_SESSION['user']['matricule'] ?? 'Utilisateur';
$nomUtilisateur = trim($_SESSION['user']['nom_utilisateur'] ?? '') ?: $matricule;
$profil = $_SESSION['user']['profil'] ?? '';
$role = $_SESSION['user']['role'] ?? '';
$initiales = strtoupper(substr($nomUtilisateur, 0, 2));
$parDate = [];
foreach ($evolution as $ligne) $parDate[$ligne['jour']] = $ligne;
$jours = $recettesGraph = $depensesGraph = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $jours[] = date('d/m', strtotime($date));
    $recettesGraph[] = (float) ($parDate[$date]['recettes'] ?? 0);
    $depensesGraph[] = (float) ($parDate[$date]['depenses'] ?? 0);
}
$soldeUsd = $stats['recettes']['USD'] - $stats['depenses']['USD'];
?>
<!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tableau de bord - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/dashboard.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/profile.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/theme.css" rel="stylesheet">
    <script src="<?= BASE_URL ?>/assets/js/theme-mode.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head><body><div class="dashboard-shell">
<div class="mobile-overlay" id="mobile-overlay" onclick="toggleSidebar()"></div>
<?php require dirname(__DIR__).'/layouts/sidebar.php'; ?>
<main class="dashboard-main">
    <header class="dashboard-topbar"><button class="menu-button" onclick="toggleSidebar()"><i class="bi bi-list"></i></button><div><h1>Tableau de bord</h1><p>Vue d’ensemble de votre activité</p></div><div class="topbar-date"><i class="bi bi-calendar3"></i> <?= date('d/m/Y') ?></div></header>
    <div class="dashboard-content">
        <section class="welcome-panel"><div><span class="eyebrow">Bienvenue</span><h2>Bonjour, <?= htmlspecialchars($nomUtilisateur) ?></h2><p>Suivez les performances de votre flotte et vos opérations financières.</p></div><a href="<?= BASE_URL ?>/recettes" class="primary-action"><i class="bi bi-plus-lg"></i> Nouvelle recette</a></section>
        <section class="stats-grid">
            <article class="stat-card"><div class="stat-top"><span>Véhicules</span><i class="bi bi-truck"></i></div><strong><?= $stats['vehicules'] ?></strong><small><?= $stats['vehicules_actifs'] ?> actif(s)</small></article>
            <article class="stat-card"><div class="stat-top"><span>Travailleurs</span><i class="bi bi-people"></i></div><strong><?= $stats['travailleurs'] ?></strong><small><?= $stats['affectations'] ?> affectation(s) active(s)</small></article>
            <article class="stat-card"><div class="stat-top"><span>Recettes</span><i class="bi bi-arrow-up-right"></i></div><strong><?= number_format($stats['recettes']['USD'], 2, ',', ' ') ?> $</strong><small><?= number_format($stats['recettes']['CDF'], 0, ',', ' ') ?> CDF</small></article>
            <article class="stat-card"><div class="stat-top"><span>Solde estimé</span><i class="bi bi-bank"></i></div><strong class="<?= $soldeUsd < 0 ? 'negative' : '' ?>"><?= number_format($soldeUsd, 2, ',', ' ') ?> $</strong><small>Recettes moins dépenses</small></article>
        </section>
        <section class="content-grid">
            <article class="panel chart-panel"><div class="panel-heading"><div><h3>Activité financière</h3><p>Recettes et dépenses USD sur 7 jours</p></div><span class="status-dot">À jour</span></div><div class="chart-wrap"><canvas id="finance-chart"></canvas></div></article>
            <article class="panel quick-panel"><div class="panel-heading"><div><h3>Actions rapides</h3><p>Accès aux opérations courantes</p></div></div><div class="quick-links">
                <a href="<?= BASE_URL ?>/recettes"><i class="bi bi-cash-coin"></i><span><strong>Ajouter une recette</strong><small>Enregistrer une entrée</small></span><i class="bi bi-chevron-right"></i></a>
                <a href="<?= BASE_URL ?>/depenses"><i class="bi bi-receipt"></i><span><strong>Ajouter une dépense</strong><small>Enregistrer une sortie</small></span><i class="bi bi-chevron-right"></i></a>
                <a href="<?= BASE_URL ?>/affectations"><i class="bi bi-arrow-left-right"></i><span><strong>Nouvelle affectation</strong><small>Associer équipe et véhicule</small></span><i class="bi bi-chevron-right"></i></a>
            </div></article>
        </section>
        <section class="panel movements-panel"><div class="panel-heading"><div><h3>Mouvements récents</h3><p>Dernières opérations enregistrées en caisse</p></div><a href="<?= BASE_URL ?>/caisse">Tout afficher</a></div><div class="table-scroll"><table><thead><tr><th>Opération</th><th>Description</th><th>Date</th><th class="amount">Montant</th><th>Statut</th></tr></thead><tbody>
            <?php if (!$mouvements): ?><tr><td colspan="5" class="empty">Aucun mouvement enregistré.</td></tr><?php endif; ?>
            <?php foreach ($mouvements as $mouvement): $entree = $mouvement['type_mouvement'] === 'ENTREE'; ?><tr><td><span class="operation-icon <?= $entree ? 'in' : 'out' ?>"><i class="bi bi-arrow-<?= $entree ? 'down-left' : 'up-right' ?>"></i></span> <?= $entree ? 'Entrée' : 'Sortie' ?></td><td><?= htmlspecialchars($mouvement['description'] ?: 'Sans description') ?></td><td><?= date('d/m/Y H:i', strtotime($mouvement['created_at'])) ?></td><td class="amount <?= $entree ? 'positive' : 'negative' ?>"><?= $entree ? '+' : '-' ?><?= number_format((float) $mouvement['montant'], 2, ',', ' ') ?> $</td><td><span class="badge-success">Validé</span></td></tr><?php endforeach; ?>
        </tbody></table></div></section>
    </div><footer class="dashboard-footer">© <?= date('Y') ?> MWIRA Trans <span>Gestion de transport</span></footer>
</main></div>
<script>
new Chart(document.getElementById('finance-chart'),{type:'line',data:{labels:<?= json_encode($jours) ?>,datasets:[{label:'Recettes',data:<?= json_encode($recettesGraph) ?>,borderColor:'#16a34a',backgroundColor:'rgba(22,163,74,.12)',fill:true,tension:.4,borderWidth:2.5,pointRadius:3},{label:'Dépenses',data:<?= json_encode($depensesGraph) ?>,borderColor:'#86efac',backgroundColor:'transparent',tension:.4,borderWidth:2.5,pointRadius:3}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top',align:'end',labels:{usePointStyle:true,boxWidth:8}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#edf4ef'},ticks:{callback:v=>v+' $'}}}}});
function toggleSidebar(){document.getElementById('dashboard-sidebar').classList.toggle('open');document.getElementById('mobile-overlay').classList.toggle('show')}
</script></body></html>
