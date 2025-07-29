<?php
require 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $user_id    = $data['user_id'] ?? '';
    $place_id   = $data['place_id'] ?? '';
    $place_type = $data['place_type'] ?? '';
    $price      = $data['price'] ?? 0;
    $features   = $data['features'] ?? '';

    if (empty($user_id) || empty($place_id) || empty($place_type)) {
        echo json_encode(["status" => false, "message" => "All booking details are required."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, place_id, place_type, price, features) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisds", $user_id, $place_id, $place_type, $price, $features);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => true,
            "message" => "Booking confirmed!",
            "booking_id" => $stmt->insert_id
        ]);
    } else {
        echo json_encode(["status" => false, "message" => "Booking failed. Try again."]);
    }

    $stmt->close();
    $conn->close();
}
?>
