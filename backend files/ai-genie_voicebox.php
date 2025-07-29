<?php
require 'config.php';
header('Content-Type: application/json');

// Read voice input from JSON
$input = json_decode(file_get_contents("php://input"), true);
$voice_input = strtolower($input['voice_input'] ?? '');

// Sanitize input
$voice_input = trim($voice_input);

// Response template
$response = [
    "status" => false,
    "message" => "Sorry, I didn't understand your request.",
    "redirect" => null,
    "filters" => []
];

// Types of selections
$keywords_map = [
    "hotel"    => "hotels.php",
    "resort"   => "resorts.php",
    "vacation" => "vacations.php",
    "trip"     => "vacations.php",
    "business" => "businesstrips.php",
];

// Facility keywords (can be used for filtering)
$facilities = ['wifi', 'ac', 'air conditioning', 'pool', 'spa', 'parking', 'breakfast', 'gym'];

// Detect redirection based on travel type
foreach ($keywords_map as $key => $file) {
    if (strpos($voice_input, $key) !== false) {
        $response['status'] = true;
        $response['message'] = "Redirecting to " . $file;
        $response['redirect'] = $file;
        break;
    }
}

// Detect facility-based filters
$matched_facilities = [];
foreach ($facilities as $facility) {
    if (strpos($voice_input, $facility) !== false) {
        $matched_facilities[] = ucfirst($facility);
    }
}
if (!empty($matched_facilities)) {
    $response['filters'] = $matched_facilities;
    if ($response['status'] === false) {
        $response['status'] = true;
        $response['message'] = "Filters detected: " . implode(", ", $matched_facilities);
    }
}

echo json_encode($response);
?>
