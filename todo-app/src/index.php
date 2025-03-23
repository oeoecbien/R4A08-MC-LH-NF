<?php
// Vérifier la connexion à la base de données
$dbHost = getenv('DB_HOST');
$dbPort = getenv('DB_PORT');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');

// Initialisation de la connexion
try {
    $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName;";
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $dbStatus = "Connecté à la base de données PostgreSQL";

    // Création de la table si elle n'existe pas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            completed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    $dbStatus = "Erreur de connexion à la base de données: " . $e->getMessage();
}

// Vérifier la connexion à Redis
$redisHost = getenv('REDIS_HOST');
$redisPort = getenv('REDIS_PORT');
try {
    $redis = new Redis();
    $redis->connect($redisHost, $redisPort);
    $redisStatus = "Connecté à Redis";
} catch (Exception $e) {
    $redisStatus = "Erreur de connexion à Redis: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1>Todo List - Docker Edition</h1>

        <div class="status">
            <p><?php echo $dbStatus; ?></p>
            <p><?php echo $redisStatus; ?></p>
        </div>

        <div class="todo-container">
            <div class="input-container">
                <input type="text" id="task-input" placeholder="Ajouter une nouvelle tâche...">
                <button id="add-task">Ajouter</button>
            </div>

            <ul id="task-list">
                <!-- Les tâches seront chargées ici par JavaScript -->
            </ul>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>

</html>