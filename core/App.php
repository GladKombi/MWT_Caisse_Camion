<?php
class App
{
    public function run(): void
    {
        $routes = require dirname(__DIR__) . '/config/routes.php';
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $base = BASE_PATH;

        if ($base !== '' && ($uri === $base || str_starts_with($uri, $base . '/'))) {
            $uri = substr($uri, strlen($base));
        }

        // Accepte les anciens favoris qui contiennent explicitement /public.
        if ($uri === '/public' || str_starts_with($uri, '/public/')) {
            $uri = substr($uri, strlen('/public'));
        }

        $uri = rtrim($uri, '/');

        if ($uri === '') {
            $uri = '/';
        }

        if (!isset($routes[$uri])) {
            http_response_code(404);
            require dirname(__DIR__) . '/views/errors/404.php';
            return;
        }

        // Seule la page de connexion est publique dans l'application MVC.
        // La page d'accueil publique est servie par Index.php à la racine.
        if ($uri !== '/login') {
            require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';
            AuthMiddleware::handle();
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
