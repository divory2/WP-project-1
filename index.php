<?php
session_start();

// Initialize session variables if not set
if (!isset($_SESSION['current_page'])) {
    $_SESSION['current_page'] = 'home';
}

// Handle form submissions and navigation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'start_game':
                $_SESSION['game_settings'] = [
                    'category' => $_POST['category'],
                    'difficulty' => $_POST['difficulty'],
                    'player_names' => array_filter($_POST['player_names'])
                ];
                $_SESSION['current_page'] = 'game';
                break;
            case 'submit_answer':
                handleAnswerSubmission();
                break;
            case 'end_game':
                saveToLeaderboard();
                $_SESSION['current_page'] = 'home';
                break;
            case 'view_leaderboard':
                $_SESSION['current_page'] = 'leaderboard';
                break;
            case 'back_to_home':
                $_SESSION['current_page'] = 'home';
                break;
            case 'clear_leaderboard':
                clearLeaderboard();
                break;
        }
    }
}

// Handle GET requests for navigation
if (isset($_GET['page'])) {
    $_SESSION['current_page'] = $_GET['page'];
}

function handleAnswerSubmission() {
    if (!isset($_SESSION['game_data'])) return;
    
    $questionIndex = $_POST['question_index'];
    $selectedAnswer = $_POST['selected_answer'];
    $currentPlayer = $_SESSION['game_data']['current_player'];
    
    // Mark question as answered
    $_SESSION['game_data']['answered_questions'][$questionIndex] = true;
    
    // Check if answer is correct
    $question = $_SESSION['game_data']['questions'][$questionIndex];
    $isCorrect = ($selectedAnswer === $question['correct_answer']);
    
    // Update player score
    if ($isCorrect) {
        $points = getPointsForDifficulty($_SESSION['game_settings']['difficulty']);
        $_SESSION['game_data']['players'][$currentPlayer]['score'] += $points;
    }
    
    // Move to next player
    $nextPlayer = ($currentPlayer + 1) % count($_SESSION['game_data']['players']);
    $_SESSION['game_data']['current_player'] = $nextPlayer;
    
    // Check if game is over
    $answeredCount = count($_SESSION['game_data']['answered_questions']);
    if ($answeredCount >= count($_SESSION['game_data']['questions'])) {
        $_SESSION['game_data']['game_ended'] = true;
    }
}

function getPointsForDifficulty($difficulty) {
    switch ($difficulty) {
        case 'easy': return 100;
        case 'medium': return 200;
        case 'hard': return 300;
        default: return 100;
    }
}

function saveToLeaderboard() {
    if (!isset($_SESSION['game_data']['players'])) return;
    
    $leaderboard = getLeaderboard();
    $entry = [
        'date' => date('Y-m-d H:i:s'),
        'players' => $_SESSION['game_data']['players'],
        'category' => $_SESSION['game_settings']['category'],
        'difficulty' => $_SESSION['game_settings']['difficulty']
    ];
    
    $leaderboard[] = $entry;
    
    // Keep only last 50 entries
    if (count($leaderboard) > 50) {
        $leaderboard = array_slice($leaderboard, -50);
    }
    
    file_put_contents('leaderboard.json', json_encode($leaderboard));
}

function getLeaderboard() {
    if (file_exists('leaderboard.json')) {
        return json_decode(file_get_contents('leaderboard.json'), true) ?: [];
    }
    return [];
}

function clearLeaderboard() {
    file_put_contents('leaderboard.json', '[]');
}

function makeApiRequest($endpoint, $params = []) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $url = $baseUrl . "/api.php?endpoint=" . $endpoint;
    
    if (!empty($params)) {
        $url .= "&" . http_build_query($params);
    }
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'JeopardyGame/1.0'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        // Fallback: try direct file inclusion
        $_GET['endpoint'] = $endpoint;
        foreach ($params as $key => $value) {
            $_GET[$key] = $value;
        }
        
        ob_start();
        include 'api.php';
        $response = ob_get_clean();
    }
    
    return json_decode($response, true);
}

function getCategories() {
    return makeApiRequest('categories');
}

function getDifficulties() {
    return makeApiRequest('difficulties');
}

function getQuestions($category, $difficulty) {
    return makeApiRequest('questions', [
        'category' => $category,
        'difficulty' => $difficulty
    ]);
}

// Initialize game data if starting new game
if ($_SESSION['current_page'] === 'game' && !isset($_SESSION['game_data'])) {
    $settings = $_SESSION['game_settings'];
    $questionsData = getQuestions($settings['category'], $settings['difficulty']);
    
    if (isset($questionsData['results'])) {
        $_SESSION['game_data'] = [
            'questions' => $questionsData['results'],
            'current_player' => 0,
            'players' => array_map(function($name) {
                return ['name' => $name, 'score' => 0];
            }, $settings['player_names']),
            'answered_questions' => [],
            'game_ended' => false
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="App">
        <?php
        switch ($_SESSION['current_page']) {
            case 'home':
                include 'pages/home.php';
                break;
            case 'game':
                include 'pages/game.php';
                break;
            case 'question':
                include 'pages/question.php';
                break;
            case 'leaderboard':
                include 'pages/leaderboard.php';
                break;
            default:
                include 'pages/home.php';
        }
        ?>
    </div>
</body>
</html> 