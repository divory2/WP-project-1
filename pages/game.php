<?php
if (!isset($_SESSION['game_data'])) {
    header('Location: ?page=home');
    exit;
}

$gameData = $_SESSION['game_data'];
$settings = $_SESSION['game_settings'];
$questions = $gameData['questions'];
$players = $gameData['players'];
$currentPlayer = $gameData['current_player'];
$answeredQuestions = $gameData['answered_questions'];
$gameEnded = $gameData['game_ended'];

// Get category name
$categories = getCategories();
$categoryName = 'Unknown';
if (isset($categories['trivia_categories'])) {
    foreach ($categories['trivia_categories'] as $cat) {
        if ($cat['id'] == $settings['category']) {
            $categoryName = $cat['name'];
            break;
        }
    }
}
?>

<div class="container">
    <div class="game-header">
        <h1>🎯 Jeopardy Game</h1>
        <div class="game-info">
            <span><strong>Category:</strong> <?php echo htmlspecialchars($categoryName); ?></span>
            <span><strong>Difficulty:</strong> <?php echo htmlspecialchars(ucfirst($settings['difficulty'])); ?></span>
        </div>
    </div>

    <?php if ($gameEnded): ?>
        <!-- Game End Screen -->
        <div class="card">
            <h2 style="text-align: center; margin-bottom: 30px;">Game Over!</h2>
            
            <div class="final-scores">
                <h3>Final Scores:</h3>
                <?php
                // Sort players by score
                usort($players, function($a, $b) {
                    return $b['score'] - $a['score'];
                });
                ?>
                <?php foreach ($players as $index => $player): ?>
                    <div class="player-score <?php echo $index === 0 ? 'winner' : ''; ?>">
                        <span class="player-name"><?php echo htmlspecialchars($player['name']); ?></span>
                        <span class="score"><?php echo $player['score']; ?> points</span>
                        <?php if ($index === 0): ?>
                            <span class="winner-badge">🏆 Winner!</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="POST" action="" style="text-align: center; margin-top: 30px;">
                <input type="hidden" name="action" value="end_game">
                <button type="submit" class="btn">Save to Leaderboard & Return Home</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Active Game -->
        <div class="game-layout">
            <!-- Players Section -->
            <div class="players-section">
                <h3>Players</h3>
                <?php foreach ($players as $index => $player): ?>
                    <div class="player <?php echo $index === $currentPlayer ? 'current' : ''; ?>">
                        <div class="player-name"><?php echo htmlspecialchars($player['name']); ?></div>
                        <div class="player-score"><?php echo $player['score']; ?> points</div>
                        <?php if ($index === $currentPlayer): ?>
                            <div class="current-indicator">👤 Current Turn</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Game Board -->
            <div class="game-board">
                <h3>Questions</h3>
                <div class="questions-grid">
                    <?php foreach ($questions as $index => $question): ?>
                        <?php $isAnswered = isset($answeredQuestions[$index]); ?>
                        <div class="question-cell <?php echo $isAnswered ? 'answered' : 'clickable'; ?>">
                            <?php if ($isAnswered): ?>
                                <span class="answered-text">✓ Answered</span>
                            <?php else: ?>
                                <a href="?page=question&index=<?php echo $index; ?>" class="question-link">
                                    <?php echo getPointsForDifficulty($settings['difficulty']); ?> pts
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.game-header {
    text-align: center;
    margin-bottom: 30px;
}

.game-info {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 10px;
}

.game-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
}

.players-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}

.player {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 5px;
    background: white;
}

.player.current {
    background: #e3f2fd;
    border: 2px solid #2196f3;
}

.current-indicator {
    font-size: 0.8em;
    color: #2196f3;
    margin-top: 5px;
}

.game-board {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}

.questions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 15px;
}

.question-cell {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    font-weight: bold;
}

.question-cell.clickable {
    background: #4caf50;
    color: white;
    cursor: pointer;
    transition: background 0.3s;
}

.question-cell.clickable:hover {
    background: #45a049;
}

.question-cell.answered {
    background: #ccc;
    color: #666;
}

.question-link {
    color: white;
    text-decoration: none;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.final-scores {
    margin: 20px 0;
}

.player-score {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    margin: 10px 0;
    background: #f8f9fa;
    border-radius: 5px;
}

.player-score.winner {
    background: #fff3cd;
    border: 2px solid #ffc107;
}

.winner-badge {
    color: #856404;
    font-weight: bold;
}
</style> 