<?php

declare(strict_types=1);

use App\Controllers\DoctorController;
use App\Core\Database;
use App\Models\Doctor;

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$config = require __DIR__ . '/../app/Config/config.php';

try {
    $connection = Database::connect($config);
    $doctorModel = new Doctor($connection);
    $controller = new DoctorController($doctorModel, $config);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'store') {
            $controller->store();
        }

        if ($action === 'store_specialty') {
            $controller->storeSpecialty();
        }

        if ($action === 'update') {
            $controller->update();
        }
    }

    $controller->index();
} catch (Throwable $exception) {
    http_response_code(500);
    echo '<h1>Error interno</h1>';
    echo '<p>' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}
