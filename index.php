<?php
$host = 'b7dy10qu16utharho4kn-mysql.services.clever-cloud.com'; // Change to your MySQL host
$dbname = 'b7dy10qu16utharho4kn'; // Your database name
$username = 'uq0lelgbxjfjwpy3'; // MySQL username
$password = 'eatfIJlwIBCbarMdLtFs'; // MySQL password (default is empty for localhost)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle the heart reaction POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['heart_id'])) {
    $confessionId = $_POST['heart_id'];

    // Fetch the current heart count for the given confession ID
    $stmt = $pdo->prepare("SELECT heart_count FROM confessions WHERE id = :id");
    $stmt->execute(['id' => $confessionId]);
    $confession = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($confession) {
        // Increment the heart count
        $newHeartCount = $confession['heart_count'] + 1;

        // Update the heart count in the database
        $updateStmt = $pdo->prepare("UPDATE confessions SET heart_count = :heart_count WHERE id = :id");
        $updateStmt->execute(['heart_count' => $newHeartCount, 'id' => $confessionId]);

        // Return a JSON response with the updated heart count
        echo json_encode([
            'success' => true,
            'newHeartCount' => $newHeartCount
        ]);
    } else {
        // If no confession found, return an error response
        echo json_encode([
            'success' => false,
            'message' => 'Confession not found'
        ]);
    }
    exit;  // Exit after processing the POST request
}

// Handle the confession submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confession_message'])) {
    $confessionMessage = trim($_POST['confession_message']);

    if (strlen($confessionMessage) >= 3 && strlen($confessionMessage) <= 500) {
        $stmt = $pdo->prepare("INSERT INTO confessions (message) VALUES (:message)");
        $stmt->execute(['message' => $confessionMessage]);

        // Return a success response
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Return an error if the message length is invalid
        echo json_encode(['success' => false, 'message' => 'Confession must be between 3 and 500 characters']);
        exit;
    }
}

// Get all confessions from the database
$stmt = $pdo->query("SELECT * FROM confessions ORDER BY created_at DESC");
$confessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONFESSION</title>
    <link rel="stylesheet" href="/assets/main.css"> </head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@100..900&family=Inconsolata:wght@200..900&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Spicy+Rice&display=swap" rel="stylesheet">
<body>

<center><h1 class="firsttext">CONFESSIONS</h1></center>

    <center>
        <!-- Button to open confession form -->
        <!-- Button to open confession form -->
        <button onclick="openForm()">UPLOAD CONFESSIONS</button>

        <!-- Confession Form -->
        <div id="confessionForm" style="display:none;">
            <textarea id="confession_message" placeholder="Write your confession..."></textarea>
            <div class="char-count">0/500</div>
            <button id="submitBtn">Submit Confession</button>
            <button onclick="closeForm()">Close</button>
        </div>

        <div id="confessionsList">
            <?php foreach ($confessions as $confession): ?>
                <div class="confession">
                    <h2>MESSAGE : <?= htmlspecialchars($confession['message']) ?></h2>
                    <div class="reaction">
                        <button class="heart-btn" data-id="<?= $confession['id'] ?>">
                            ❤️ <span>REACTS : <?= $confession['heart_count'] ?></span> <!-- Display heart count -->
                        </button>
                    </div>
                    <p class="posted-date"><?= $confession['created_at'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

    </center>
    <script src="confessions.js"></script>
</body>
</html>