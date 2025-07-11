# 🎯 Jeopardy Showdown - Pure PHP Version

A server-side rendered Jeopardy Showdown built with **PHP, HTML, and CSS only** - no JavaScript required!

## Features

- 🎮 **Pure PHP Game Logic** - All game mechanics handled server-side
- 🎯 **Trivia Questions** - Powered by Open Trivia Database API
- 👥 **Multi-Player Support** - Up to 3 players per game
- 🏆 **Leaderboard System** - Track game history and scores
- 📱 **Responsive Design** - Works on desktop and mobile
- 🎨 **Modern UI** - Beautiful gradient design with smooth animations
- 💾 **Session-Based Storage** - No database required

## How to Play

1. **Setup Game**: Choose a category, difficulty, and enter player names
2. **Take Turns**: Players take turns selecting and answering questions
3. **Earn Points**: Correct answers earn points based on difficulty
4. **Win**: Player with the most points at the end wins!

## File Structure

```
├── index.php              # Main application file
├── api.php                # API proxy for trivia questions
├── style.css              # Styling and animations
├── pages/                 # Page templates
│   ├── home.php          # Game setup page
│   ├── game.php          # Main game board
│   ├── question.php      # Individual question display
│   └── leaderboard.php   # Score history
├── leaderboard.json      # Game history storage
└── README.md             # This file
```

## Installation & Setup

### Local Development

1. **Start PHP Server**:
   ```bash
   php -S localhost:8000
   ```

2. **Open Browser**:
   ```
   http://localhost:8000
   ```

### Web Hosting Deployment

1. **Upload Files**: Upload all files to your web hosting directory
2. **Set Permissions**: Ensure `leaderboard.json` is writable
3. **Access**: Navigate to your domain to start playing

## Game Rules

- **Easy Questions**: 100 points
- **Medium Questions**: 200 points  
- **Hard Questions**: 300 points
- **Turn Order**: Players take turns clockwise
- **Game End**: When all questions are answered
- **Winner**: Player with highest score

## Technical Details

- **Backend**: PHP 7.4+ with session management
- **Frontend**: HTML5 + CSS3 (no JavaScript)
- **API**: Open Trivia Database (via proxy)
- **Storage**: JSON file for leaderboard
- **Styling**: Custom CSS with gradients and animations

## API Integration

The game uses the Open Trivia Database API through a PHP proxy (`api.php`) to:
- Fetch question categories
- Get difficulty levels
- Retrieve random questions

## Browser Compatibility

- ✅ Chrome/Chromium
- ✅ Firefox
- ✅ Safari
- ✅ Edge
- ✅ Mobile browsers

## License

This project is open source and available under the MIT License.

---

**Enjoy playing Jeopardy! 🎯** 