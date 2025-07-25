<?php
require 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$booking_id = $input['booking_id'] ?? '';
$action     = strtolower($input['action'] ?? '');

if (empty($booking_id) || !in_array($action, ['confirm', 'cancel'])) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid booking ID or action."
    ]);
    exit;
}

if ($action == "cancel") {
    // Delete or mark the booking as canceled
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $success = $stmt->execute();

    echo json_encode([
        "status" => $success,
        "message" => $success ? "Booking cancelled successfully." : "Cancellation failed."
    ]);
} else {
    // Confirm the booking
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $success = $stmt->execute();

    echo json_encode([
        "status" => $success,
        "message" => $success ? "Booking confirmed successfully." : "Confirmation failed."
    ]);
}

$stmt->close();
$conn->close();
?>
