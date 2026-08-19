<?php
/**
 * Générateur MVC - Gestion Camion
 * Compatible PHP 8+
 */

declare(strict_types=1);

$projectName = 'gestion_camion';
$basePath = __DIR__ . DIRECTORY_SEPARATOR . $projectName;

$directories = [
    'config',
    'core',
    'controllers',
    'models',
    'services',
    'middleware',
    'helpers',
    'views/layouts',
    'views/auth',
    'views/dashboard',
    'views/travailleurs',
    'views/fonctions',
    'views/vehicules',
    'views/affectations',
    'views/recettes',
    'views/depenses',
    'views/categories-depenses',
    'views/remunerations',
    'views/caisse',
    'views/utilisateurs',
    'views/rapports',
    'views/audit',
    'views/errors',
    'public/assets/css',
    'public/assets/js',
    'public/assets/img/vehicles',
    'public/assets/uploads/travailleurs',
    'public/assets/uploads/vehicules',
    'public/assets/uploads/justificatifs',
    'storage/logs',
    'storage/exports',
    'storage/reports',
    'database/seeders',
    'database/backups',
];

$files = [];

$files['index.php'] = <<<'PHP'
<?php
header('Location: public/');
exit;
PHP;

$files['.htaccess'] = <<<'HTACCESS'
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/gestion_camion/public/
RewriteRule ^(.*)$ public/$1 [L]
HTACCESS;

$files['README.md'] = <<<'MD'
# Gestion Camion

Application PHP MVC pour la gestion des travailleurs, véhicules, affectations, recettes, dépenses, rémunérations, caisse, utilisateurs, rapports et audit.

## Installation

1. Copier le projet dans `htdocs`.
2. Créer/importer la base `gestion_camion`.
3. Configurer `config/database.php`.
4. Démarrer Apache et MySQL.
5. Accéder à `http://localhost/gestion_camion/`.
MD;

$files['config/database.php'] = <<<'PHP'
<?php
return [
    'host' => 'localhost',
    'dbname' => 'gestion_camion',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
PHP;

$files['config/config.php'] = <<<'PHP'
<?php

define('APP_NAME', 'Gestion Camion');
define('BASE_URL', 'http://localhost/gestion_camion/public');
define('ROOT_PATH', dirname(__DIR__));
define('VIEW_PATH', ROOT_PATH . '/views');
define('STORAGE_PATH', ROOT_PATH . '/storage');

date_default_timezone_set('Africa/Lubumbashi');
PHP;

$files['config/routes.php'] = <<<'PHP'
<?php
return [
    '/' => ['controller' => 'DashboardController', 'method' => 'index'],
    '/login' => ['controller' => 'AuthController', 'method' => 'login'],
    '/logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    '/dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
    '/travailleurs' => ['controller' => 'TravailleurController', 'method' => 'index'],
    '/fonctions' => ['controller' => 'FonctionController', 'method' => 'index'],
    '/vehicules' => ['controller' => 'VehiculeController', 'method' => 'index'],
    '/affectations' => ['controller' => 'AffectationVehiculeController', 'method' => 'index'],
    '/recettes' => ['controller' => 'RecetteController', 'method' => 'index'],
    '/depenses' => ['controller' => 'DepenseController', 'method' => 'index'],
    '/categories-depenses' => ['controller' => 'CategorieDepenseController', 'method' => 'index'],
    '/remunerations' => ['controller' => 'RemunerationController', 'method' => 'index'],
    '/caisse' => ['controller' => 'CaisseController', 'method' => 'index'],
    '/utilisateurs' => ['controller' => 'UtilisateurController', 'method' => 'index'],
    '/rapports' => ['controller' => 'RapportController', 'method' => 'index'],
    '/audit' => ['controller' => 'AuditController', 'method' => 'index'],
];
PHP;

$files['core/Database.php'] = <<<'PHP'
<?php
class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require dirname(__DIR__) . '/config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['dbname'],
                $config['charset']
            );

            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$instance;
    }
}
PHP;

$files['core/Model.php'] = <<<'PHP'
<?php
require_once __DIR__ . '/Database.php';

class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}
PHP;

$files['core/Controller.php'] = <<<'PHP'
<?php
class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            require dirname(__DIR__) . '/views/errors/404.php';
            return;
        }

        require $viewFile;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
}
PHP;

$files['core/App.php'] = <<<'PHP'
<?php
class App
{
    public function run(): void
    {
        $routes = require dirname(__DIR__) . '/config/routes.php';
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $base = '/gestion_camion/public';
        $uri = str_replace($base, '', $uri);
        $uri = rtrim($uri, '/');

        if ($uri === '') {
            $uri = '/';
        }

        if (!isset($routes[$uri])) {
            http_response_code(404);
            require dirname(__DIR__) . '/views/errors/404.php';
            return;
        }

        $route = $routes[$uri];
        $controllerName = $route['controller'];
        $method = $route['method'];
        $controllerFile = dirname(__DIR__) . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            throw new RuntimeException('Contrôleur introuvable : ' . $controllerName);
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            throw new RuntimeException('Méthode introuvable : ' . $method);
        }

        $controller->$method();
    }
}
PHP;

$files['core/Auth.php'] = <<<'PHP'
<?php
class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}
PHP;

$files['core/Session.php'] = <<<'PHP'
<?php
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }
}
PHP;

$files['core/Validator.php'] = <<<'PHP'
<?php
class Validator
{
    public static function required(mixed $value): bool
    {
        return trim((string)$value) !== '';
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
PHP;

$files['core/Helper.php'] = <<<'PHP'
<?php
class Helper
{
    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
PHP;

$files['core/Router.php'] = <<<'PHP'
<?php
class Router
{
    public static function url(string $path = ''): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }
}
PHP;

$models = [
    'Travailleur',
    'FonctionTravailleur',
    'AttributionFonction',
    'Vehicule',
    'AffectationVehicule',
    'CategorieDepense',
    'RemunerationTravailleur',
    'Utilisateur',
    'Recette',
    'Depense',
    'MouvementCaisse',
    'JournalAudit',
];

foreach ($models as $model) {
    $files['models/' . $model . '.php'] = "<?php\nrequire_once __DIR__ . '/../core/Model.php';\n\nclass {$model} extends Model\n{\n}\n";
}

$files['models/Travailleur.php'] = <<<'PHP'
<?php
require_once __DIR__ . '/../core/Model.php';

class Travailleur extends Model
{
    public function getAll(): array
    {
        $sql = "SELECT * FROM travailleurs WHERE statut != 'SUPPRIME' ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
PHP;

$files['models/Vehicule.php'] = <<<'PHP'
<?php
require_once __DIR__ . '/../core/Model.php';

class Vehicule extends Model
{
    public function getAll(): array
    {
        $sql = 'SELECT * FROM vehicules WHERE statut_supprime = 0 ORDER BY id DESC';
        return $this->db->query($sql)->fetchAll();
    }
}
PHP;

$files['models/Utilisateur.php'] = <<<'PHP'
<?php
require_once __DIR__ . '/../core/Model.php';

class Utilisateur extends Model
{
    public function findByMatricule(string $matricule): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM utilisateurs WHERE matricule = ? AND statut = 0 LIMIT 1'
        );
        $stmt->execute([$matricule]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
PHP;

$files['controllers/AuthController.php'] = <<<'PHP'
<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Utilisateur.php';

class AuthController extends Controller
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricule = trim($_POST['matricule'] ?? '');
            $password = $_POST['password'] ?? '';

            $model = new Utilisateur();
            $user = $model->findByMatricule($matricule);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'matricule' => $user['matricule'],
                    'role' => $user['role'],
                ];

                $this->redirect('/dashboard');
            }

            $error = 'Matricule ou mot de passe incorrect.';
            $this->view('auth/login', compact('error'));
            return;
        }

        $this->view('auth/login');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}
PHP;

$controllerMap = [
    'DashboardController' => 'dashboard/index',
    'FonctionController' => 'fonctions/index',
    'AffectationVehiculeController' => 'affectations/index',
    'RecetteController' => 'recettes/index',
    'DepenseController' => 'depenses/index',
    'CategorieDepenseController' => 'categories-depenses/index',
    'RemunerationController' => 'remunerations/index',
    'CaisseController' => 'caisse/index',
    'UtilisateurController' => 'utilisateurs/index',
    'RapportController' => 'rapports/index',
    'AuditController' => 'audit/index',
];

foreach ($controllerMap as $controller => $view) {
    $files['controllers/' . $controller . '.php'] = "<?php\nrequire_once dirname(__DIR__) . '/core/Controller.php';\n\nclass {$controller} extends Controller\n{\n    public function index(): void\n    {\n        \$this->view('{$view}');\n    }\n}\n";
}

$files['controllers/TravailleurController.php'] = <<<'PHP'
<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Travailleur.php';

class TravailleurController extends Controller
{
    public function index(): void
    {
        $model = new Travailleur();
        $travailleurs = $model->getAll();
        $this->view('travailleurs/index', compact('travailleurs'));
    }
}
PHP;

$files['controllers/VehiculeController.php'] = <<<'PHP'
<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Vehicule.php';

class VehiculeController extends Controller
{
    public function index(): void
    {
        $model = new Vehicule();
        $vehicules = $model->getAll();
        $this->view('vehicules/index', compact('vehicules'));
    }
}
PHP;

$services = ['AuthService', 'CaisseService', 'RemunerationService', 'RapportService', 'AuditService'];
foreach ($services as $service) {
    $files['services/' . $service . '.php'] = "<?php\nclass {$service}\n{\n}\n";
}

$files['middleware/AuthMiddleware.php'] = <<<'PHP'
<?php
class AuthMiddleware
{
    public static function handle(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
PHP;

$files['middleware/AdminMiddleware.php'] = <<<'PHP'
<?php
class AdminMiddleware
{
    public static function handle(): void
    {
        if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
            http_response_code(403);
            require dirname(__DIR__) . '/views/errors/403.php';
            exit;
        }
    }
}
PHP;

$files['middleware/ComptableMiddleware.php'] = <<<'PHP'
<?php
class ComptableMiddleware
{
    public static function handle(): void
    {
        $roles = ['ADMIN', 'COMPTABLE'];
        if (!in_array($_SESSION['user']['role'] ?? '', $roles, true)) {
            http_response_code(403);
            require dirname(__DIR__) . '/views/errors/403.php';
            exit;
        }
    }
}
PHP;

$files['middleware/RoleMiddleware.php'] = <<<'PHP'
<?php
class RoleMiddleware
{
    public static function check(array $roles): bool
    {
        return in_array($_SESSION['user']['role'] ?? null, $roles, true);
    }
}
PHP;

$files['helpers/auth_helper.php'] = <<<'PHP'
<?php
function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}
PHP;

$files['helpers/date_helper.php'] = <<<'PHP'
<?php
function format_date(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}
PHP;

$files['helpers/money_helper.php'] = <<<'PHP'
<?php
function format_money(float $amount, string $currency = 'USD'): string
{
    return number_format($amount, 2, ',', ' ') . ' ' . $currency;
}
PHP;

$files['helpers/flash_helper.php'] = <<<'PHP'
<?php
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}
PHP;

$files['helpers/url_helper.php'] = <<<'PHP'
<?php
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}
PHP;

$files['views/layouts/header.php'] = <<<'PHP'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
PHP;

$files['views/layouts/navbar.php'] = <<<'PHP'
<nav class="navbar navbar-dark bg-primary px-3">
    <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard">
        <i class="bi bi-truck"></i> Gestion Camion
    </a>
    <span class="text-white">
        <?= htmlspecialchars($_SESSION['user']['matricule'] ?? 'Utilisateur') ?>
    </span>
</nav>
PHP;

$files['views/layouts/sidebar.php'] = <<<'PHP'
<div class="sidebar">
    <a href="<?= BASE_URL ?>/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="<?= BASE_URL ?>/travailleurs"><i class="bi bi-people"></i> Travailleurs</a>
    <a href="<?= BASE_URL ?>/fonctions"><i class="bi bi-person-badge"></i> Fonctions</a>
    <a href="<?= BASE_URL ?>/vehicules"><i class="bi bi-truck"></i> Véhicules</a>
    <a href="<?= BASE_URL ?>/affectations"><i class="bi bi-arrow-left-right"></i> Affectations</a>
    <a href="<?= BASE_URL ?>/recettes"><i class="bi bi-cash-coin"></i> Recettes</a>
    <a href="<?= BASE_URL ?>/depenses"><i class="bi bi-wallet2"></i> Dépenses</a>
    <a href="<?= BASE_URL ?>/categories-depenses"><i class="bi bi-tags"></i> Catégories</a>
    <a href="<?= BASE_URL ?>/remunerations"><i class="bi bi-currency-dollar"></i> Rémunérations</a>
    <a href="<?= BASE_URL ?>/caisse"><i class="bi bi-bank"></i> Caisse</a>
    <a href="<?= BASE_URL ?>/rapports"><i class="bi bi-bar-chart"></i> Rapports</a>
    <a href="<?= BASE_URL ?>/utilisateurs"><i class="bi bi-person-gear"></i> Utilisateurs</a>
    <a href="<?= BASE_URL ?>/audit"><i class="bi bi-journal-text"></i> Audit</a>
    <a href="<?= BASE_URL ?>/logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
</div>
PHP;

$files['views/layouts/footer.php'] = <<<'PHP'
<footer class="text-center py-4 text-muted">
    &copy; <?= date('Y') ?> Gestion Camion
</footer>
PHP;

$files['views/layouts/scripts.php'] = <<<'PHP'
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
PHP;

$files['views/auth/login.php'] = <<<'PHP'
<?php require dirname(__DIR__) . '/layouts/header.php'; ?>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-truck fs-1 text-primary"></i>
                        <h3>Gestion Camion</h3>
                        <p class="text-muted">Connexion</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/login">
                        <div class="mb-3">
                            <label class="form-label">Matricule</label>
                            <input type="text" name="matricule" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Connexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require dirname(__DIR__) . '/layouts/scripts.php'; ?>
PHP;

$files['views/auth/forgot-password.php'] = '<h1>Mot de passe oublié</h1>';

$files['views/dashboard/index.php'] = <<<'PHP'
<?php
require dirname(__DIR__) . '/layouts/header.php';
require dirname(__DIR__) . '/layouts/navbar.php';
require dirname(__DIR__) . '/layouts/sidebar.php';
?>
<main class="main-content">
    <div class="container-fluid py-4">
        <h2 class="mb-4">Tableau de bord</h2>
        <div class="row g-3">
            <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Véhicules</h6><h2>0</h2></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Travailleurs</h6><h2>0</h2></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Recettes</h6><h2>0 USD</h2></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Dépenses</h6><h2>0 USD</h2></div></div></div>
        </div>
    </div>
</main>
<?php
require dirname(__DIR__) . '/layouts/footer.php';
require dirname(__DIR__) . '/layouts/scripts.php';
?>
PHP;

$files['views/travailleurs/index.php'] = <<<'PHP'
<?php
require dirname(__DIR__) . '/layouts/header.php';
require dirname(__DIR__) . '/layouts/navbar.php';
require dirname(__DIR__) . '/layouts/sidebar.php';
?>
<main class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Travailleurs</h2>
            <a href="#" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Ajouter</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach (($travailleurs ?? []) as $travailleur): ?>
                            <tr>
                                <td><?= htmlspecialchars($travailleur['matricule']) ?></td>
                                <td><?= htmlspecialchars(trim(($travailleur['nom'] ?? '') . ' ' . ($travailleur['postnom'] ?? '') . ' ' . ($travailleur['prenom'] ?? ''))) ?></td>
                                <td><?= htmlspecialchars($travailleur['telephone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($travailleur['statut']) ?></td>
                                <td><button class="btn btn-sm btn-warning">Modifier</button></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require dirname(__DIR__) . '/layouts/footer.php';
require dirname(__DIR__) . '/layouts/scripts.php';
?>
PHP;

$simpleViews = [
    'fonctions/index.php' => 'Fonctions',
    'vehicules/index.php' => 'Gestion des véhicules',
    'affectations/index.php' => 'Affectations des véhicules',
    'recettes/index.php' => 'Recettes',
    'depenses/index.php' => 'Dépenses',
    'categories-depenses/index.php' => 'Catégories de dépenses',
    'remunerations/index.php' => 'Rémunérations',
    'caisse/index.php' => 'Caisse',
    'utilisateurs/index.php' => 'Utilisateurs',
    'rapports/index.php' => 'Rapports',
    'audit/index.php' => "Journal d'audit",
];

foreach ($simpleViews as $path => $title) {
    $files['views/' . $path] = '<h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
}

$emptyViews = [
    'travailleurs/create.php', 'travailleurs/edit.php', 'travailleurs/show.php', 'travailleurs/affectations.php',
    'fonctions/create.php', 'fonctions/edit.php',
    'vehicules/create.php', 'vehicules/edit.php', 'vehicules/show.php', 'vehicules/affectations.php',
    'affectations/create.php', 'affectations/edit.php', 'affectations/show.php',
    'recettes/create.php', 'recettes/edit.php', 'recettes/show.php',
    'depenses/create.php', 'depenses/edit.php', 'depenses/show.php',
    'categories-depenses/create.php', 'categories-depenses/edit.php',
    'remunerations/calcul.php', 'remunerations/details.php', 'remunerations/historique.php',
    'caisse/mouvements.php', 'caisse/situation.php',
    'utilisateurs/create.php', 'utilisateurs/edit.php', 'utilisateurs/profil.php',
    'rapports/recettes.php', 'rapports/depenses.php', 'rapports/vehicules.php',
    'rapports/travailleurs.php', 'rapports/remunerations.php', 'rapports/caisse.php',
];

foreach ($emptyViews as $view) {
    $files['views/' . $view] = "<?php\n// Vue à compléter.\n";
}

$files['views/errors/403.php'] = '<h1>403 - Accès interdit</h1>';
$files['views/errors/404.php'] = '<h1>404 - Page introuvable</h1>';
$files['views/errors/500.php'] = '<h1>500 - Erreur serveur</h1>';

$files['public/index.php'] = <<<'PHP'
<?php
session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/core/App.php';
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/Database.php';

try {
    $app = new App();
    $app->run();
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Erreur serveur</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}
PHP;

$files['public/.htaccess'] = <<<'HTACCESS'
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
HTACCESS;

$files['public/assets/css/style.css'] = <<<'CSS'
body {
    background: #f5f7fb;
}

.sidebar {
    position: fixed;
    top: 56px;
    left: 0;
    width: 240px;
    height: calc(100vh - 56px);
    background: #fff;
    border-right: 1px solid #e5e5e5;
    padding: 20px 10px;
    overflow-y: auto;
}

.sidebar a {
    display: block;
    padding: 12px 15px;
    margin-bottom: 5px;
    color: #343a40;
    text-decoration: none;
    border-radius: 8px;
}

.sidebar a:hover {
    background: #0d6efd;
    color: #fff;
}

.sidebar i {
    margin-right: 8px;
}

.main-content {
    margin-left: 240px;
}

.card {
    border-radius: 12px;
}

@media (max-width: 768px) {
    .sidebar {
        display: none;
    }

    .main-content {
        margin-left: 0;
    }
}
CSS;

$files['public/assets/css/dashboard.css'] = '';
$files['public/assets/css/responsive.css'] = '';
$files['public/assets/js/app.js'] = "document.addEventListener('DOMContentLoaded', () => console.log('Gestion Camion MVC chargé.'));\n";
$files['public/assets/js/dashboard.js'] = '';
$files['public/assets/js/datatable.js'] = '';
$files['public/assets/js/theme.js'] = '';
$files['storage/logs/application.log'] = '';
$files['database/gestion_camion.sql'] = "-- Collez ici votre script SQL gestion_camion.\n";
$files['database/seeders/utilisateurs.sql'] = "-- Utilisateurs de démonstration.\n";

function makeDirectory(string $path): bool
{
    return is_dir($path) || mkdir($path, 0777, true);
}

function html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if (!makeDirectory($basePath)) {
    die('Impossible de créer le dossier principal : ' . html($basePath));
}

$results = [];

foreach ($directories as $directory) {
    $fullPath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $directory);
    $results[] = [
        'type' => 'Dossier',
        'name' => $directory,
        'ok' => makeDirectory($fullPath),
    ];
}

foreach ($files as $file => $content) {
    $fullPath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    makeDirectory(dirname($fullPath));

    $written = file_put_contents($fullPath, $content);
    $results[] = [
        'type' => 'Fichier',
        'name' => $file,
        'ok' => $written !== false,
    ];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création Gestion Camion</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; padding: 30px; }
        .container { max-width: 950px; margin: auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,.08); }
        h1 { color: #0d6efd; }
        .ok { color: #198754; }
        .error { color: #dc3545; }
        .path { font-family: monospace; background: #f1f1f1; padding: 3px 7px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    </style>
</head>
<body>
<div class="container">
    <h1>Création du projet Gestion Camion</h1>
    <p>Projet : <span class="path"><?= html($basePath) ?></span></p>

    <table>
        <thead>
        <tr>
            <th>Type</th>
            <th>Élément</th>
            <th>Résultat</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $result): ?>
            <tr>
                <td><?= html($result['type']) ?></td>
                <td><?= html($result['name']) ?></td>
                <td class="<?= $result['ok'] ? 'ok' : 'error' ?>">
                    <?= $result['ok'] ? '✓ Créé' : '✗ Erreur' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Terminé</h2>
    <p>Ouvre ensuite :</p>
    <p><a href="/gestion_camion/" target="_blank">http://localhost/gestion_camion/</a></p>
</div>
</body>
</html>
