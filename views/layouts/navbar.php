<nav class="navbar navbar-dark bg-primary px-3">
    <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard">
        <i class="bi bi-truck"></i> Gestion Camion
    </a>
    <span class="text-white">
        <?= htmlspecialchars($_SESSION['user']['matricule'] ?? 'Utilisateur') ?>
    </span>
</nav>