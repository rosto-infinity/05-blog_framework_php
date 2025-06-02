<?php

use Libraries\Router;
use Libraries\Controllers\Article;
use Libraries\Controllers\Comment;
use Libraries\Controllers\User;

$router = new Router();

// -Page d'accueil
$router->get('/', [Article::class, 'index']);

// -Affichage d'un article
$router->get('/article', [Article::class, 'show']);

// -Ajout d'un commentaire
$router->post('/comment/save', [Comment::class, 'save']);

// -Suppression d'un commentaire
$router->get('/comment/delete', [Comment::class, 'delete']);

// -Authentification
$router->get('/login', [User::class, 'login']);
$router->post('/login', [User::class, 'login']);
$router->get('/logout', [User::class, 'logout']);

// Inscription
$router->get('/register', [User::class, 'register']);
$router->post('/register', [User::class, 'register']);

// ...ajoute d'autres routes ici

return $router;
