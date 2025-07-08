<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Function to fetch questions from Open Trivia Database
function fetchQuestions($difficulty, $count) {
    $url = "https://opentdb.com/api.php?amount={$count}&difficulty={$difficulty}&type=multiple";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return false;
    }
    
    return json_decode($response, true);
}

// Function to assign point values based on difficulty
function assignPointValue($difficulty, $index) {
    $baseValues = [
        'easy' => [100, 200, 300, 400, 500],
        'medium' => [200, 400, 600, 800, 1000],
        'hard' => [300, 600, 900, 1200, 1500]
    ];
    
    $values = $baseValues[$difficulty];
    return $values[$index % count($values)];
}

// Function to categorize questions
function categorizeQuestions($questions) {
    $categories = [
        'General Knowledge' => 'General Knowledge',
        'Entertainment: Books' => 'Literature',
        'Entertainment: Film' => 'Movies',
        'Entertainment: Music' => 'Music',
        'Entertainment: Musicals & Theatres' => 'Theater',
        'Entertainment: Television' => 'TV Shows',
        'Entertainment: Video Games' => 'Video Games',
        'Entertainment: Board Games' => 'Board Games',
        'Science & Nature' => 'Science',
        'Science: Computers' => 'Technology',
        'Science: Mathematics' => 'Mathematics',
        'Mythology' => 'Mythology',
        'Sports' => 'Sports',
        'Geography' => 'Geography',
        'History' => 'History',
        'Politics' => 'Politics',
        'Art' => 'Art',
        'Celebrities' => 'Celebrities',
        'Animals' => 'Animals',
        'Vehicles' => 'Vehicles',
        'Entertainment: Comics' => 'Comics',
        'Science: Gadgets' => 'Gadgets',
        'Entertainment: Japanese Anime & Manga' => 'Anime',
        'Entertainment: Cartoon & Animations' => 'Cartoons'
    ];
    
    foreach ($questions as &$question) {
        $question['category'] = $categories[$question['category']] ?? 'Miscellaneous';
    }
    
    return $questions;
}

// Main API logic
try {
    // Validate input parameters
    $difficulty = $_GET['difficulty'] ?? 'medium';
    $count = intval($_GET['count'] ?? 10);
    
    // Validate difficulty
    $validDifficulties = ['easy', 'medium', 'hard'];
    if (!in_array($difficulty, $validDifficulties)) {
        throw new Exception('Invalid difficulty level');
    }
    
    // Validate count
    if ($count < 1 || $count > 50) {
        throw new Exception('Invalid question count (1-50)');
    }
    
    // Fetch questions from external API
    $apiResponse = fetchQuestions($difficulty, $count);
    
    if (!$apiResponse || $apiResponse['response_code'] !== 0) {
        throw new Exception('Failed to fetch questions from external API');
    }
    
    $questions = $apiResponse['results'];
    
    // Process questions
    $processedQuestions = [];
    foreach ($questions as $index => $question) {
        $processedQuestions[] = [
            'category' => $question['category'],
            'question' => html_entity_decode($question['question']),
            'correct_answer' => html_entity_decode($question['correct_answer']),
            'incorrect_answers' => array_map('html_entity_decode', $question['incorrect_answers']),
            'value' => assignPointValue($difficulty, $index)
        ];
    }
    
    // Categorize questions
    $processedQuestions = categorizeQuestions($processedQuestions);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'questions' => $processedQuestions,
        'total' => count($processedQuestions),
        'difficulty' => $difficulty
    ]);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 