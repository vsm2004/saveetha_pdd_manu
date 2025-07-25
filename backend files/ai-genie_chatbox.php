<?php
require 'config.php';
header('Content-Type: application/json');

// Get user input
$input = json_decode(file_get_contents("php://input"), true);
$message = strtolower(trim($input['message'] ?? ''));

// Default response
$response = [
    "status" => false,
    "reply" => "I'm not sure I understand. Can you rephrase?"
];

// Keyword to redirect/file map
$routes = [
    "hotel"    => "hotels.php",
    "resort"   => "resorts.php",
    "vacation" => "vacations.php",
    "trip"     => "vacations.php",
    "business" => "businesstrips.php",
];

// Facility detection
$facilities = ['wifi', 'ac', 'pool', 'spa', 'parking', 'gym', 'breakfast'];
$matched_facilities = [];

foreach ($facilities as $facility) {
    if (strpos($message, $facility) !== false) {
        $matched_facilities[] = ucfirst($facility);
    }
}

// Determine redirect
foreach ($routes as $key => $file) {
    if (strpos($message, $key) !== false) {
        $response = [
            "status" => true,
            "reply" => "Sure! Redirecting you to " . ucfirst(str_replace(".php", "", $file)),
            "redirect" => $file,
            "filters" => $matched_facilities
        ];
        break;
    }
}

// If facilities found but no travel type
if (!$response['status'] && count($matched_facilities) > 0) {
    $response = [
        "status" => true,
        "reply" => "I see you're looking for: " . implode(", ", $matched_facilities) . ". Please mention hotel/resort/vacation.",
        "filters" => $matched_facilities
    ];
}

echo json_encode($response);
?>
