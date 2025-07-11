<?php
$categories = getCategories();
$difficulties = getDifficulties();

$error = '';
$success = '';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Check if API calls failed
if (!$categories || !$difficulties) {
    $error = 'Unable to load game settings. Please check your internet connection and try again.';
}
?>

<div class="container">
    <div class="card">
        <h1>🎯 Jeopardy Showdown</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
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
                    >
                <?php endfor; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Start Game</button>
                <button type="submit" name="action" value="view_leaderboard" class="btn btn-secondary">
                    View Leaderboard
                </button>
            </div>
        </form>
    </div>
</div> 