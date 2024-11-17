<?php
$host = 'b7dy10qu16utharho4kn-mysql.services.clever-cloud.com'; // Change to your MySQL host
$dbname = 'b7dy10qu16utharho4kn'; // Your database name
$username = 'uq0lelgbxjfjwpy3'; // MySQL username
$password = 'eatfIJlwIBCbarMdLtFs'; // MySQL password (default is empty for localhost)

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confession_message = trim($_POST['confession_message']);

    // Validate message length (between 3 and 500 characters)
    if (strlen($confession_message) < 3 || strlen($confession_message) > 500) {
        die("Confession message must be between 3 and 500 characters.");
    }

    // Insert confession into database
    $stmt = $pdo->prepare("INSERT INTO confessions (confession_message) VALUES (:message)");
    $stmt->execute(['message' => $confession_message]);

    // Redirect to prevent form resubmission
    header("Location: index.php");
    exit();
}
?>
