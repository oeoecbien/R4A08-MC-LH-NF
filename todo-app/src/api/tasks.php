<?php
header('Content-Type: application/json');

// Connexion à la base de données
$dbHost = getenv('DB_HOST');
$dbPort = getenv('DB_PORT');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');

try {
    $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName;";
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit;
}

// Connexion à Redis pour le cache
$redisHost = getenv('REDIS_HOST');
$redisPort = getenv('REDIS_PORT');
try {
    $redis = new Redis();
    $redis->connect($redisHost, $redisPort);
} catch (Exception $e) {
    // En cas d'erreur Redis, on continue sans cache
}

// Déterminer l'action à effectuer
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Vérifier si les données sont en cache
        $cacheKey = 'tasks';
        if (isset($redis) && $redis->exists($cacheKey)) {
            echo $redis->get($cacheKey);
            exit;
        }

        // Récupérer les tâches depuis la base de données
        $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mettre en cache pour 60 secondes
        if (isset($redis)) {
            $redis->setex($cacheKey, 60, json_encode($tasks));
        }

        echo json_encode($tasks);
        break;

    case 'POST':
        // Récupérer les données envoyées
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['title']) || empty(trim($data['title']))) {
            echo json_encode(['error' => 'Le titre de la tâche est requis']);
            exit;
        }

        // Ajouter la tâche
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title) RETURNING id, title, completed, created_at");
        $stmt->execute(['title' => $data['title']]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        // Invalider le cache
        if (isset($redis)) {
            $redis->del('tasks');
        }

        echo json_encode($task);
        break;

    case 'PUT':
        // Récupérer l'ID et les données
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$id) {
            echo json_encode(['error' => 'ID de tâche requis']);
            exit;
        }

        // Mettre à jour la tâche
        $stmt = $pdo->prepare("UPDATE tasks SET completed = :completed WHERE id = :id RETURNING id, title, completed, created_at");
        $stmt->execute([
            'id' => $id,
            'completed' => $data['completed'] ?? false
        ]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        // Invalider le cache
        if (isset($redis)) {
            $redis->del('tasks');
        }

        echo json_encode($task);
        break;

    case 'DELETE':
        // Récupérer l'ID
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(['error' => 'ID de tâche requis']);
            exit;
        }

        // Supprimer la tâche
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Invalider le cache
        if (isset($redis)) {
            $redis->del('tasks');
        }

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    default:
        echo json_encode(['error' => 'Méthode non supportée']);
}
