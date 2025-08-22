<?php

// Path to the Laravel application
$basePath = __DIR__;

// Change to the application directory
chdir($basePath);

// Include the autoloader
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Connect to the database
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_DATABASE') ?: 'pos';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'latitude'");
    $latitudeExists = $stmt->fetch() !== false;

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'longitude'");
    $longitudeExists = $stmt->fetch() !== false;

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'location_updated_at'");
    $locationUpdatedAtExists = $stmt->fetch() !== false;

    // Add the columns if they don't exist
    if (!$latitudeExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN latitude DECIMAL(10, 7) NULL AFTER language");
        echo "Added latitude column to users table.\n";
    } else {
        echo "latitude column already exists in users table.\n";
    }

    if (!$longitudeExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN longitude DECIMAL(10, 7) NULL AFTER latitude");
        echo "Added longitude column to users table.\n";
    } else {
        echo "longitude column already exists in users table.\n";
    }

    if (!$locationUpdatedAtExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN location_updated_at TIMESTAMP NULL AFTER longitude");
        echo "Added location_updated_at column to users table.\n";
    } else {
        echo "location_updated_at column already exists in users table.\n";
    }

    // Mark the migration as run
    $migrationName = '2025_08_16_000000_add_location_fields_to_users_table';
    $stmt = $pdo->prepare("SELECT * FROM migrations WHERE migration = ?");
    $stmt->execute([$migrationName]);
    $migrationExists = $stmt->fetch() !== false;

    if (!$migrationExists) {
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, (SELECT MAX(batch) FROM migrations))");
        $stmt->execute([$migrationName]);
        echo "Marked migration as run in migrations table.\n";
    } else {
        echo "Migration already marked as run in migrations table.\n";
    }

    echo "Operation completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
