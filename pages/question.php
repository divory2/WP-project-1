<?php
// Check if game data and settings exist
if (!isset($_SESSION['game_data']) || !isset($_SESSION['game_settings']) || !isset($_GET['index'])) {
    $_SESSION['error'] = 'Please start a new game first.';
    header('Location: ?page=home');
    exit;
}

$questionIndex = (int)$_GET['index'];
$gameData = $_SESSION['game_data'];
$settings = $_SESSION['game_settings'];

// Validate game data structure
if (!isset($gameData['questions']) || !isset($gameData['players']) || !isset($gameData['current_player'])) {
    $_SESSION['error'] = 'Game data is corrupted. Please start a new game.';
    header('Location: ?page=home');
    exit;
}

$questions = $gameData['questions'];

if (!isset($questions[$questionIndex])) {
    $_SESSION['error'] = 'Question not found.';
    header('Location: ?page=game');
    exit;
}

$question = $questions[$questionIndex];

// Check if question was already answered
$answeredQuestions = $gameData['answered_questions'] ?? [];
if (isset($answeredQuestions[$questionIndex])) {
    $_SESSION['error'] = 'This question has already been answered.';
    header('Location: ?page=game');
    exit;
}

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_answer'])) {
    $_POST['question_index'] = $questionIndex;
    $_POST['action'] = 'submit_answer';
    
    // Include the answer handling logic
    handleAnswerSubmission();
    
    // Redirect back to game
    header('Location: ?page=game');
    exit;
}

// Prepare answers (correct + incorrect)
$answers = array_merge([$question['correct_answer']], $question['incorrect_answers']);
shuffle($answers);
?>

<div class="container">
    <div class="card question-card">
        <div class="question-header">
            <h2>Question <?php echo $questionIndex + 1; ?></h2>
            <div class="question-info">
                <span class="points"><?php echo getPointsForDifficulty($settings['difficulty']); ?> points</span>
                <span class="current-player">Current: <?php echo htmlspecialchars($gameData['players'][$gameData['current_player']]['name'] ?? 'Unknown'); ?></span>
            </div>
        </div>

        <div class="question-content">
            <h3><?php echo htmlspecialchars($question['question']); ?></h3>
        </div>

        <form method="POST" action="" class="answers-form">
            <div class="answers-grid">
                <?php foreach ($answers as $index => $answer): ?>
                    <div class="answer-option">
                        <input type="radio" name="selected_answer" id="answer_<?php echo $index; ?>" value="<?php echo htmlspecialchars($answer); ?>" required>
                        <label for="answer_<?php echo $index; ?>" class="answer-label">
                            <?php echo htmlspecialchars($answer); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="question-actions">
                <button type="submit" class="btn">Submit Answer</button>
                <a href="?page=game" class="btn btn-secondary">Back to Game</a>
            </div>
        </form>
    </div>
</div>

<style>
.question-card {
    max-width: 800px;
    margin: 0 auto;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.question-info {
    display: flex;
    gap: 20px;
}

.points {
    background: #28a745;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
}

.current-player {
    color: #2196f3;
    font-weight: bold;
}

.question-content {
    margin-bottom: 30px;
}

.question-content h3 {
    font-size: 1.3rem;
    line-height: 1.6;
    color: #333;
}

.answers-grid {
    display: grid;
    gap: 15px;
    margin-bottom: 30px;
}

.answer-option {
    display: flex;
    align-items: center;
}

.answer-option input[type="radio"] {
    display: none;
}

.answer-label {
    display: block;
    width: 100%;
    padding: 15px 20px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1.1rem;
}

.answer-label:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.answer-option input[type="radio"]:checked + .answer-label {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.question-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}
</style> 