<?php
require 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $selection = $_POST['selection'] ?? '';

    // Basic validation
    if (empty($user_id) || empty($selection)) {
        echo json_encode([
            "status" => false,
            "message" => "User ID and selection are required."
        ]);
        exit;
    }

    // Insert selection
    $stmt = $conn->prepare("INSERT INTO dashboard_selections (user_id, selection) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $selection);

    if ($stmt->execute()) {
        // Suggest redirect based on selection
        $redirect_map = [
            'hotel' => 'hotels.php',
            'resort' => 'resorts.php',
            'vacation' => 'vacations.php',
            'business' => 'businesstrips.php'
        ];
        $redirect = $redirect_map[strtolower($selection)] ?? 'dashboard.php';

        echo json_encode([
            "status" => true,
            "message" => "Selection recorded.",
            "redirect" => $redirect
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Failed to save selection."
        ]);
    }

    $stmt->close();
    $conn->close();
}
?>
