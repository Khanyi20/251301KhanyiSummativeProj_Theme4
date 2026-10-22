```php
<?php
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_DATABASE') ?: 'pawsitive';
$dbuser = getenv('DB_USERNAME') ?: 'root';
$dbpass = getenv('DB_PASSWORD') ?: '';

try {
    $conn = new PDO(
        "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    die("Database connection failed. Please check the database configuration.");
}
?>
```