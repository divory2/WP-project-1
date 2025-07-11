# Jeopardy Game - Plain PHP Version

A complete Jeopardy game built with **plain PHP and React** - no frameworks, no database required!

## Features

- 🎯 **3-player Jeopardy game** with real trivia questions
- 🏆 **Leaderboard** stored in browser localStorage
- 📱 **Responsive design** that works on all devices
- 🌐 **Real-time questions** from Open Trivia Database API
- 🎨 **Modern UI** with smooth animations
- 📊 **Score tracking** and player turns
- 🏅 **Game history** with detailed results

## How It Works

- **PHP API Proxy** (`api.php`) - Fetches questions from external API
- **React Frontend** - Handles all game logic and UI
- **Local Storage** - Stores leaderboard data in browser
- **No Database** - Everything works without any server-side storage

## Files

```
jeopardy-php/
├── index.html          # Main HTML file
├── style.css           # All styling
├── app.js              # React application
├── api.php             # PHP API proxy
└── README.md           # This file
```

## Quick Start

1. **Upload to your PHP hosting:**
   - Upload all files to your web root
   - Make sure `index.html` is accessible

2. **That's it!** No database setup, no dependencies to install.

## API Endpoints

The `api.php` file handles these endpoints:

- `GET api.php?endpoint=categories` - Get trivia categories
- `GET api.php?endpoint=difficulties` - Get difficulty levels
- `GET api.php?endpoint=questions&category=9&difficulty=easy&amount=15` - Get questions

## Hosting Requirements

### Required:
- ✅ **PHP 7.4+** (for API proxy)
- ✅ **Web server** (Apache, Nginx, etc.)
- ✅ **Internet connection** (for external API calls)

### Not Required:
- ❌ **Database** (MySQL, PostgreSQL, etc.)
- ❌ **Node.js** or build tools
- ❌ **Composer** or PHP dependencies
- ❌ **Special server software**

## Game Rules

1. **3 players** take turns answering questions
2. **15 questions total** (5 per player)
3. **Points based on difficulty:**
   - Easy: 100 points
   - Medium: 200 points
   - Hard: 300 points
4. **Correct answers** add points
5. **Wrong answers** subtract points (minimum 0)
6. **Winner** is the player with the highest score

## Leaderboard

- **Stored in browser** localStorage
- **Top 10 games** are kept
- **Per-browser** (not shared between devices)
- **Includes** date, difficulty, category, and final standings

## Customization

### Styling
Edit `style.css` to change colors, fonts, and layout.

### Game Settings
Modify `app.js` to change:
- Number of questions
- Point values
- Game board layout

### API
Update `api.php` to:
- Add new endpoints
- Change external API
- Add caching

## Troubleshooting

### Questions Not Loading
- Check if your hosting allows external API calls
- Verify `api.php` is accessible
- Check browser console for errors

### Styling Issues
- Ensure `style.css` is in the same directory as `index.html`
- Check if your hosting supports CSS files

### Game Not Working
- Make sure all files are uploaded
- Check browser console for JavaScript errors
- Verify React and Axios are loading from CDN

## Browser Support

- ✅ **Chrome** 60+
- ✅ **Firefox** 55+
- ✅ **Safari** 12+
- ✅ **Edge** 79+

## License

This project is open source and available under the MIT License.

---

**Perfect for shared hosting, VPS, or any PHP hosting service!** 