# Plain PHP Jeopardy Showdown - Deployment Guide

## Overview
This is a **completely self-contained** Jeopardy Showdown that works with just PHP and HTML. No database, no frameworks, no build tools required!

## What You Get
- ✅ **4 files total** - That's it!
- ✅ **No dependencies** - Everything included via CDN
- ✅ **Works anywhere** - Any PHP hosting service

## Files to Upload

```
jeopardy-php/
├── index.html          # Main game page
├── style.css           # All styling
├── app.js              # React game logic
└── api.php             # PHP API proxy
```

## Step-by-Step Deployment

### 1. Prepare Files
- Download all 4 files
- Keep them in the same folder

### 2. Upload to Hosting
- Upload all files to your web root (usually `public_html` or `www`)
- Make sure `index.html` is in the root directory

### 3. Test
- Visit your domain (e.g., `https://yourdomain.com`)
- The game should load immediately

## Hosting Requirements

### Minimum Requirements:
- ✅ **PHP 7.4+** (for the API proxy)
- ✅ **Web server** (Apache, Nginx, etc.)
- ✅ **Internet access** (for external trivia API)

### What You DON'T Need:
- ❌ **Database** (MySQL, PostgreSQL, etc.)
- ❌ **Node.js** or npm
- ❌ **Composer** or PHP packages
- ❌ **Build tools** or compilation
- ❌ **Special server software**

## How It Works

### Data Flow:
1. **User visits site** → `index.html` loads
2. **React app starts** → Loads from CDN
3. **Game setup** → Calls `api.php` for categories/difficulties
4. **Game starts** → Calls `api.php` for questions

### API Calls:
- `api.php?endpoint=categories` → Gets trivia categories
- `api.php?endpoint=difficulties` → Gets difficulty levels
- `api.php?endpoint=questions&category=9&difficulty=easy&amount=15` → Gets questions

## Testing Your Deployment

### 1. Check File Access
Visit these URLs to ensure files are accessible:
- `https://yourdomain.com/` (should show the game)
- `https://yourdomain.com/api.php?endpoint=categories` (should return JSON)

### 2. Test Game Features
- Select category and difficulty
- Enter player names
- Start a game
- Answer questions
- Check leaderboard

### 3. Check Browser Console
- Open Developer Tools (F12)
- Look for any JavaScript errors
- Verify API calls are working

## Troubleshooting

### Game Not Loading
**Problem:** Blank page or errors
**Solutions:**
- Check if all 4 files are uploaded
- Verify `index.html` is in the root directory
- Check browser console for errors
- Ensure your hosting supports PHP

### API Not Working
**Problem:** "Failed to load game settings" error
**Solutions:**
- Check if `api.php` is accessible
- Verify your hosting allows external API calls
- Test `api.php?endpoint=categories` directly
- Check PHP error logs

### Styling Issues
**Problem:** Game looks broken or unstyled
**Solutions:**
- Ensure `style.css` is in the same directory as `index.html`
- Check if your hosting blocks CSS files
- Verify CDN links are accessible

### Questions Not Loading
**Problem:** Game starts but no questions appear
**Solutions:**
- Check if external API calls are allowed
- Verify internet connectivity
- Test the questions endpoint directly
- Check browser console for network errors

## Common Hosting Services

### Shared Hosting (cPanel, etc.)
- Upload files to `public_html/`
- Should work immediately
- No special configuration needed

### VPS/Dedicated Server
- Upload to web root (usually `/var/www/html/`)
- Ensure PHP is installed and enabled
- Check file permissions (644 for files, 755 for directories)

### Cloud Hosting (AWS, Google Cloud, etc.)
- Upload to web server directory
- Ensure PHP is configured
- Check security groups/firewall for external API access

## File Permissions

Set these permissions on your hosting:
```bash
chmod 644 index.html
chmod 644 style.css
chmod 644 app.js
chmod 644 api.php
```

## Security Notes

- **No sensitive data** is stored on the server
- **No user accounts** or authentication required
- **No database** means no SQL injection risks
- **External API calls** are read-only

## Performance

- **Lightweight** - Only 4 files
- **Fast loading** - React and libraries from CDN
- **No server processing** - Game logic runs in browser
- **Minimal bandwidth** - Only API calls for questions

## Customization

### Change Colors
Edit `style.css`:
```css
body {
    background: linear-gradient(135deg, #your-color 0%, #your-color 100%);
}
```

### Change Game Settings
Edit `app.js`:
```javascript
// Change number of questions
amount: 15 // Change this value

// Change point values
case 'easy': return 100; // Change point values
```

### Add New Features
- Modify `app.js` for new game features
- Update `api.php` for new API endpoints
- Edit `style.css` for new styling

## Support

If you encounter issues:
1. Check browser console for errors
2. Test API endpoints directly
3. Verify all files are uploaded correctly
4. Check your hosting service's PHP support
5. Ensure external API calls are allowed

---

**This setup is perfect for any PHP hosting service - no database required!** 