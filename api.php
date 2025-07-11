<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'categories':
        $response = file_get_contents('https://opentdb.com/api_category.php');
        if ($response === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch categories']);
        } else {
            echo $response;
        }
        break;
        
    case 'difficulties':
        echo json_encode([
            'difficulties' => [
                ['id' => 'easy', 'name' => 'Easy'],
                ['id' => 'medium', 'name' => 'Medium'],
                ['id' => 'hard', 'name' => 'Hard']
            ]
        ]);
        break;
        
    case 'questions':
        $category = $_GET['category'] ?? 9;
        $difficulty = $_GET['difficulty'] ?? 'easy';
        $amount = $_GET['amount'] ?? 15;
        
        $url = "https://opentdb.com/api.php?amount=$amount&category=$category&difficulty=$difficulty&type=multiple";
        $response = file_get_contents($url);
        
        if ($response === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch questions']);
        } else {
            echo $response;
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid endpoint']);
}
?> 