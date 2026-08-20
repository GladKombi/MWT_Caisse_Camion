<?php
$sidebarUser=$_SESSION['user']??[];$sidebarName=trim((string)($sidebarUser['nom_utilisateur']??''))?:($sidebarUser['matricule']??'Utilisateur');$sidebarMatricule=$sidebarUser['matricule']??'';$sidebarProfile=$sidebarUser['profil']??'';$sidebarRole=$sidebarUser['role']??'';$sidebarPath=parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH)?:'';
$isActive=static fn(string$path):string=>preg_match('#/'.preg_quote(trim($path,'/'),'#').'/?$#',$sidebarPath)?'active':'';
?>
<aside class="dashboard-sidebar" id="dashboard-sidebar">
<a class="brand" href="<?= BASE_URL ?>/dashboard"><span class="brand-icon"><i class="bi bi-truck"></i></span><span><strong>MWIRA Trans</strong><small>Gestion de transport</small></span></a>
<nav class="dashboard-nav" aria-label="Navigation principale">
<?php if($sidebarRole==='EMPLOYE'): ?>
<span class="nav-heading">Espace personnel</span><a class="<?= $isActive('mon-espace') ?>" href="<?= BASE_URL ?>/mon-espace"><i class="bi bi-person-workspace"></i> Mes mouvements</a>
<?php else: ?>
<span class="nav-heading">Vue générale</span><a class="<?= $isActive('dashboard') ?>" href="<?= BASE_URL ?>/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
<span class="nav-heading">Exploitation</span><a class="<?= $isActive('vehicules') ?>" href="<?= BASE_URL ?>/vehicules"><i class="bi bi-truck"></i> Véhicules</a><a class="<?= $isActive('travailleurs') ?>" href="<?= BASE_URL ?>/travailleurs"><i class="bi bi-people"></i> Travailleurs</a><a class="<?= $isActive('affectations') ?>" href="<?= BASE_URL ?>/affectations"><i class="bi bi-arrow-left-right"></i> Affectations</a>
<span class="nav-heading">Finances</span><a class="<?= $isActive('recettes') ?>" href="<?= BASE_URL ?>/recettes"><i class="bi bi-graph-up-arrow"></i> Recettes</a><a class="<?= $isActive('depenses') ?>" href="<?= BASE_URL ?>/depenses"><i class="bi bi-wallet2"></i> Dépenses</a><a class="<?= $isActive('caisse') ?>" href="<?= BASE_URL ?>/caisse"><i class="bi bi-bank"></i> Caisse</a><a class="<?= $isActive('rapports') ?>" href="<?= BASE_URL ?>/rapports"><i class="bi bi-bar-chart"></i> Rapports</a>
<?php if($sidebarRole==='ADMIN'): ?><span class="nav-heading">Administration</span><a class="<?= $isActive('utilisateurs') ?>" href="<?= BASE_URL ?>/utilisateurs"><i class="bi bi-person-gear"></i> Utilisateurs</a><a class="<?= $isActive('audit') ?>" href="<?= BASE_URL ?>/audit"><i class="bi bi-journal-text"></i> Audit</a><?php endif; ?>
<?php endif; ?>
</nav><div class="sidebar-user"><span class="avatar"><?php if($sidebarProfile):?><img src="<?= BASE_URL.'/'.htmlspecialchars($sidebarProfile) ?>" alt="Profil"><?php else:?><?= htmlspecialchars(strtoupper(substr($sidebarName,0,2))) ?><?php endif;?></span><span class="user-copy"><strong><?= htmlspecialchars($sidebarName) ?></strong><small><?= htmlspecialchars($sidebarMatricule) ?></small></span><a class="logout" href="<?= BASE_URL ?>/logout" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a></div>
</aside>
