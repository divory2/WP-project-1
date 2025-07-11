<?php
$leaderboard = getLeaderboard();

// Get category names for display
$categories = getCategories();
$categoryNames = [];
if (isset($categories['trivia_categories'])) {
    foreach ($categories['trivia_categories'] as $cat) {
        $categoryNames[$cat['id']] = $cat['name'];
    }
}
?>

<div class="container">
    <div class="card">
        <div class="leaderboard-header">
            <h1>🏆 Leaderboard</h1>
            
            <form method="POST" action="" class="form-actions">
                <input type="hidden" name="action" value="clear_leaderboard">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear the leaderboard?')">
                    Clear Leaderboard
                </button>
            </form>
        </div>

        <?php if (empty($leaderboard)): ?>
            <div class="empty-leaderboard">
                <p>No games played yet. Start a game to see scores here!</p>
                <a href="?page=home" class="btn">Start New Game</a>
            </div>
        <?php else: ?>
            <div class="leaderboard-entries">
                <?php foreach (array_reverse($leaderboard) as $index => $entry): ?>
                    <div class="leaderboard-entry">
                        <div class="entry-header">
                            <div class="entry-date">
                                <strong>Game #<?php echo count($leaderboard) - $index; ?></strong>
                                <span class="date"><?php echo date('M j, Y g:i A', strtotime($entry['date'])); ?></span>
                            </div>
                            <div class="entry-info">
                                <span class="category"><?php echo htmlspecialchars($categoryNames[$entry['category']] ?? 'Unknown'); ?></span>
                                <span class="difficulty difficulty-<?php echo $entry['difficulty']; ?>">
                                    <?php echo ucfirst($entry['difficulty']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="entry-scores">
                            <?php
                            // Sort players by score for this entry
                            $sortedPlayers = $entry['players'];
                            usort($sortedPlayers, function($a, $b) {
                                return $b['score'] - $a['score'];
                            });
                            ?>
                            <?php foreach ($sortedPlayers as $playerIndex => $player): ?>
                                <div class="player-entry <?php echo $playerIndex === 0 ? 'winner' : ''; ?>">
                                    <span class="player-name">
                                        <?php echo htmlspecialchars($player['name']); ?>
                                        <?php if ($playerIndex === 0): ?>
                                            <span class="winner-icon">👑</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="player-score"><?php echo $player['score']; ?> points</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="?page=home" class="btn">Back to Home</a>
        </div>
    </div>
</div>

<style>
.leaderboard-header {
    margin-bottom: 30px;
}

.empty-leaderboard {
    text-align: center;
    padding: 40px;
    color: #666;
}

.leaderboard-entries {
    max-height: 600px;
    overflow-y: auto;
}

.leaderboard-entry {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
}

.entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.entry-date {
    display: flex;
    flex-direction: column;
}

.date {
    font-size: 0.9em;
    color: #666;
    margin-top: 2px;
}

.entry-info {
    display: flex;
    gap: 10px;
    align-items: center;
}

.category {
    background: #e9ecef;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.difficulty {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
    font-weight: bold;
    color: white;
}

.difficulty-easy {
    background: #28a745;
}

.difficulty-medium {
    background: #ffc107;
    color: #212529;
}

.difficulty-hard {
    background: #dc3545;
}

.entry-scores {
    display: grid;
    gap: 8px;
}

.player-entry {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: white;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

.player-entry.winner {
    background: #fff3cd;
    border-color: #ffc107;
}

.player-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: bold;
}

.winner-icon {
    font-size: 1.2em;
}

.player-score {
    font-weight: bold;
    color: #007bff;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}
</style> 