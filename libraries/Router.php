<?php

namespace Libraries;

/**
 * Routeur HTTP simple pour une application MVC
 * 
 * Responsable de :
 * - Stocker les routes définies
 * - -Rediriger les requêtes entrantes vers les bons contrôleurs
 * - Gérer les erreurs 404
 */
class Router
{
    /**
     * --Tableau contenant toutes les routes enregistrées
     * @var array
     * Format :
     * [
     *    ['method' => 'GET', 'uri' => '/chemin', 'action' => [Controller::class, 'methode']],
     *    ...
     * ]
     */
    protected $routes = [];

    /**
     * Enregistre une route GET
     * 
     * @param string $uri Chemin de la route (ex: '/articles')
     * @param array $action Tableau [Controller::class, 'methodName']
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Enregistre une route POST
     * 
     * @param string $uri Chemin de la route
     * @param array $action Tableau [Controller::class, 'methodName']
     */
    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Ajoute une route à la collection
     * 
     * @param string $method Méthode HTTP (GET/POST/etc.)
     * @param string $uri Chemin URI
     * @param array $action Callable sous forme [Controller, method]
     */
    protected function addRoute($method, $uri, $action)
    {
        // Validation basique de l'action
        if (!is_array($action) || count($action) !== 2) {
            throw new \InvalidArgumentException("L'action doit être un tableau [Controller, method]");
        }

        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $this->normalizeUri($uri),
            'action' => $action
        ];
    }

    /**
     * Traite la requête entrante et appelle le contrôleur approprié
     * 
     * @return mixed Réponse du contrôleur
     */
    public function dispatch()
    {
        $requestUri = $this->getCurrentUri();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestUri)) {
                return $this->callAction($route['action']);
            }
        }

        $this->sendNotFoundResponse();
    }

    /**
     * Vérifie si une route correspond à la requête
     * 
     * @param array $route Route à vérifier
     * @param string $method Méthode HTTP
     * @param string $uri URI demandée
     * @return bool
     */
    protected function matchRoute($route, $method, $uri)
    {
        return $route['method'] === $method && $route['uri'] === $uri;
    }

    /**
     * Instancie un contrôleur et appelle une méthode
     * 
     * @param array $action [ControllerClass, 'methodName']
     * @return mixed
     */
    protected function callAction($action)
    {
        [$controllerClass, $method] = $action;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Classe contrôleur introuvable: $controllerClass");
        }

        if (!method_exists($controllerClass, $method)) {
            throw new \RuntimeException("Méthode $method introuvable dans $controllerClass");
        }

        $controller = new $controllerClass();
        return $controller->$method();
    }

    /**
     * Normalise une URI (supprime les slashs finaux)
     * 
     * @param string $uri
     * @return string
     */
    protected function normalizeUri($uri)
    {
        return rtrim($uri, '/') ?: '/';
    }

    /**
     * Récupère l'URI actuelle depuis la requête
     * 
     * @return string
     */
    protected function getCurrentUri()
    {
        return $this->normalizeUri(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    /**
     * Envoie une réponse 404
     */
    protected function sendNotFoundResponse()
    {
        http_response_code(404);
        header('Content-Type: text/html');
        echo "<h1>404 Not Found</h1>";
        echo "<p>La page demandée n'existe pas.</p>";
        exit;
    }
}
