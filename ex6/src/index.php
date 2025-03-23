 <?php
require "../vendor/autoload.php";

// Chargement des variables d'environnement (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$status = [];
$errors = [];

// REDIS
$redis = new \Predis\Client([
    'scheme' => 'tcp',
    'host'   => $_SERVER['REDIS_HOST'],
    'port'   => $_SERVER['REDIS_PORT'],
]);


try {
    /** @var \Predis\Response\Status $response */
    $response = $redis->ping();
    $status['redis'] = $response->getPayload() === 'PONG';
} catch (\Exception $e) {
    $status['redis'] = FALSE;
    $errors['redis'] = $e->getMessage();
}



// PDO db1

try {
    $mysql = new PDO('mysql:dbname=' . $_SERVER['DB1_NAME'] . ';host=' . $_SERVER['DB1_HOST'], $_SERVER['DB1_USER'], $_SERVER['DB1_PASS']);
    $status['DB1'] = TRUE;
}
catch (\Exception $e) {
    $status['DB1'] = FALSE;
    $errors['DB1'] = $e->getMessage();
}
// PDO db2

try {
    $mysql = new PDO('pgsql:dbname=' . $_SERVER['DB2_NAME'] . ';host=' . $_SERVER['DB2_HOST'], $_SERVER['DB2_USER'], $_SERVER['DB2_PASS']);
    $status['DB2'] = TRUE;
}
catch (\Exception $e) {
    $status['DB2'] = FALSE;
    $errors['DB2'] = $e->getMessage();
}

echo "<ul>";
foreach ($status as $key => $statu) {
    echo "<li>$key : " . ($statu ? '🟩' : '🟥') . '</li>';
}
echo "</ul>";

if(count($errors)) {
    echo "<h1>Tu veux du log d'erreur ?</h1>";
    echo "<ul>";
    foreach ($errors as $key => $error) {
        echo "<li>$key : $error";
    }
    echo "</ul>";
}

echo "<h1>Un peu de debug avec l'ensemble des variables d'environnement</h1>";
echo "<pre>";
var_dump($_SERVER);
echo "<pre>";
