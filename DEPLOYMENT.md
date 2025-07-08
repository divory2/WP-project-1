# Jeopardy Game Deployment Guide

## Overview
This is a web-based Jeopardy game that uses HTML, CSS, JavaScript, and PHP to create an interactive trivia game experience.

## Requirements
- Web server with PHP support (Apache, Nginx, etc.)
- PHP 7.4 or higher
- cURL extension enabled
- Modern web browser with JavaScript enabled

## Installation

### 1. Server Setup
1. Upload all files to your web server's document root or a subdirectory
2. Ensure the web server has read permissions for all files
3. Make sure PHP is properly configured and running

### 2. File Structure
```
/
├── index.html          # Main game interface
├── style.css           # Game styling
├── app.js              # Game logic and functionality
├── api.php             # PHP API for fetching questions
├── README.md           # Project documentation
└── DEPLOYMENT.md       # This file
```

### 3. Configuration
No additional configuration is required. The game uses the Open Trivia Database API for questions.

## Features
- **Multi-player Support**: 1-6 players
- **Difficulty Levels**: Easy, Medium, Hard
- **Customizable Questions**: 5-25 questions per game
- **Real-time Scoring**: Track scores throughout the game
- **Responsive Design**: Works on desktop and mobile devices
- **External API Integration**: Uses Open Trivia Database for questions

## API Endpoints

### GET /api.php
Fetches trivia questions from the Open Trivia Database.

**Parameters:**
- `difficulty` (string): "easy", "medium", or "hard"
- `count` (integer): Number of questions (1-50)

**Example:**
```
GET /api.php?difficulty=medium&count=10
```

**Response:**
```json
{
  "success": true,
  "questions": [
    {
      "category": "Science",
      "question": "What is the chemical symbol for gold?",
      "correct_answer": "Au",
      "incorrect_answers": ["Ag", "Fe", "Cu"],
      "value": 200
    }
  ],
  "total": 10,
  "difficulty": "medium"
}
```

## Game Rules
1. Players take turns answering questions
2. Correct answers add points to the player's score
3. Incorrect answers subtract points from the player's score
4. The player with the highest score at the end wins
5. Point values vary based on difficulty level

## Troubleshooting

### Common Issues

1. **Questions not loading**
   - Check if cURL is enabled in PHP
   - Verify internet connectivity
   - Check browser console for JavaScript errors

2. **Game not starting**
   - Ensure JavaScript is enabled in the browser
   - Check that all files are properly uploaded
   - Verify file permissions

3. **Styling issues**
   - Clear browser cache
   - Check if CSS file is accessible
   - Verify file paths are correct

### Error Messages
- "Failed to fetch questions from external API": Network or API issue
- "Invalid difficulty level": Use "easy", "medium", or "hard"
- "Invalid question count": Use a number between 1 and 50

## Security Considerations
- The application uses client-side JavaScript for game logic
- No user data is stored on the server
- API calls are made directly from the client to the external trivia API
- Consider implementing rate limiting for production use

## Performance Optimization
- Enable gzip compression on the web server
- Use a CDN for static assets in production
- Implement caching headers for static files
- Consider minifying CSS and JavaScript files

## Browser Compatibility
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Support
For issues or questions, please refer to the README.md file or contact the development team.

## License
This project is open source and available under the MIT License. 