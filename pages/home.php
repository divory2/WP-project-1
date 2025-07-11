<?php
$categories = getCategories();
$difficulties = getDifficulties();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Check if API calls failed
if (!$categories || !$difficulties) {
    $error = 'Unable to load game settings. Please check your internet connection and try again.';
}
?>

<div class="container">
    <div class="card">
        <h1 style="text-align: center; margin-bottom: 30px; font-size: 2.5rem; color: #333;">
            🎯 Jeopardy Game
        </h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="start_game">
            
            <div class="form-group">
                <label for="category">Select Category:</label>
                <select id="category" name="category" class="form-control" required>
                    <?php if (isset($categories['trivia_categories'])): ?>
                        <?php foreach ($categories['trivia_categories'] as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="difficulty">Select Difficulty:</label>
                <select id="difficulty" name="difficulty" class="form-control" required>
                    <?php if (isset($difficulties['difficulties'])): ?>
                        <?php foreach ($difficulties['difficulties'] as $difficulty): ?>
                            <option value="<?php echo htmlspecialchars($difficulty['id']); ?>">
                                <?php echo htmlspecialchars($difficulty['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Player Names (3 players):</label>
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <input
                        type="text"
                        name="player_names[]"
                        class="form-control"
                        placeholder="Player <?php echo $i + 1; ?> Name"
                        required
                        style="margin-bottom: 10px;"
                    >
                <?php endfor; ?>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn">Start Game</button>
                <button type="submit" name="action" value="view_leaderboard" class="btn btn-secondary" style="margin-left: 15px;">
                    View Leaderboard
                </button>
            </div>
        </form>
    </div>
</div> 