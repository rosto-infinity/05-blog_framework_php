<?php

namespace Libraries;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    // -Valeurs par défaut encapsulées dans la classe
    private const DEFAULT_CONFIG = [
        'DB_SERVERNAME' => '127.0.0.1',
        'DB_USERNAME'   => 'valet',
        'DB_PASSWORD'   => 'valet',
        'DB_DATABASE'   => 'blog-cfpc'
    ];

    /**
     * -Charge les variables d'environnement depuis .env si disponible
     */
    private static function loadEnv(): void
    {
        $envPath = dirname(__DIR__) . '/.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (!str_contains($line, '=')) continue;
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }
            }
        }
    }

    /**
     * Retourne la connexion PDO (singleton)
     *
     * @return PDO
     */
    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            self::loadEnv();
            try {
                self::$pdo = new PDO(
                    sprintf(
                        "mysql:host=%s;dbname=%s;charset=utf8",
                        self::getConfigValue('DB_SERVERNAME'),
                        self::getConfigValue('DB_DATABASE')
                    ),
                    self::getConfigValue('DB_USERNAME'),
                    self::getConfigValue('DB_PASSWORD')
                );
                
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new \RuntimeException("Database connection error", 0, $e);
            }
        }
        return self::$pdo;
    }

    /**
     * Récupère une valeur de configuration avec fallback
     * 
     * @param string $key
     * @return mixed
     */
    private static function getConfigValue(string $key)
    {
        // Mappe les clés internes vers les clés du .env
        $envMap = [
            'DB_SERVERNAME' => 'DB_HOST',
            'DB_USERNAME'   => 'DB_USER',
            'DB_PASSWORD'   => 'DB_PASS',
            'DB_DATABASE'   => 'DB_NAME'
        ];
        if (isset($envMap[$key]) && isset($_ENV[$envMap[$key]])) {
            return $_ENV[$envMap[$key]];
        }
        return self::DEFAULT_CONFIG[$key];
    }

    /**
     * -Récupère l'ID utilisateur d'un commentaire
     *
     * @param int $comment_id
     * @return int|null
     */
    public static function getCommentUserId(int $comment_id): ?int
    {
        $pdo = self::getPdo();
        $query = $pdo->prepare('SELECT user_id FROM comments WHERE id = :comment_id');
        $query->execute(['comment_id' => $comment_id]);
        
        return ($comment = $query->fetch()) ? (int)$comment['user_id'] : null;
    }
}
