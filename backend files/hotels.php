<?php
require 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $destination = $_GET['destination'] ?? null;

    if ($destination) {
        $stmt = $conn->prepare("SELECT * FROM hotels WHERE location = ?");
        $stmt->bind_param("s", $destination);
    } else {
        $stmt = $conn->prepare("SELECT * FROM hotels LIMIT 5"); // default few hotels
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }

    echo json_encode([
        "status" => true,
        "message" => count($hotels) . " hotels found",
        "data" => $hotels
    ]);

    $stmt->close();
    $conn->close();
}
?>
