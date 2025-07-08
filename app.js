class JeopardyGame {
    constructor() {
        this.players = [];
        this.currentPlayer = 0;
        this.questions = [];
        this.categories = [];
        this.gameStarted = false;
        this.answeredQuestions = new Set();
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        const setupForm = document.getElementById('setup-form');
        setupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.startGame();
        });
    }

    async startGame() {
        const numPlayers = parseInt(document.getElementById('num-players').value);
        const difficulty = document.getElementById('difficulty').value;
        const numQuestions = parseInt(document.getElementById('num-questions').value);

        // Initialize players
        this.players = [];
        for (let i = 0; i < numPlayers; i++) {
            this.players.push({
                id: i + 1,
                name: `Player ${i + 1}`,
                score: 0
            });
        }

        try {
            // Fetch questions from API
            const response = await fetch(`api.php?difficulty=${difficulty}&count=${numQuestions}`);
            const data = await response.json();
            
            if (data.success) {
                this.questions = data.questions;
                this.categories = [...new Set(this.questions.map(q => q.category))];
                this.displayGameBoard();
                this.updateScoreBoard();
                this.gameStarted = true;
            } else {
                alert('Error loading questions: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error loading questions. Please try again.');
        }
    }

    displayGameBoard() {
        document.getElementById('game-setup').style.display = 'none';
        document.getElementById('game-board').style.display = 'block';

        // Display categories
        const categoriesDiv = document.getElementById('categories');
        categoriesDiv.innerHTML = '';
        this.categories.forEach(category => {
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'category';
            categoryDiv.textContent = category;
            categoriesDiv.appendChild(categoryDiv);
        });

        // Display questions
        this.displayQuestions();
    }

    displayQuestions() {
        const questionsDiv = document.getElementById('questions');
        questionsDiv.innerHTML = '';

        this.questions.forEach((question, index) => {
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question';
            questionDiv.textContent = `$${question.value}`;
            questionDiv.dataset.index = index;
            
            if (this.answeredQuestions.has(index)) {
                questionDiv.classList.add('answered');
            } else {
                questionDiv.addEventListener('click', () => this.showQuestion(index));
            }
            
            questionsDiv.appendChild(questionDiv);
        });
    }

    showQuestion(index) {
        const question = this.questions[index];
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h3>${question.category}</h3>
                <p class="question-text">${question.question}</p>
                <div class="options">
                    <button class="option" data-correct="false">${question.incorrect_answers[0]}</button>
                    <button class="option" data-correct="false">${question.incorrect_answers[1]}</button>
                    <button class="option" data-correct="false">${question.incorrect_answers[2]}</button>
                    <button class="option" data-correct="true">${question.correct_answer}</button>
                </div>
                <div class="current-player">
                    Current Player: ${this.players[this.currentPlayer].name}
                </div>
            </div>
        `;

        // Shuffle options
        const options = modal.querySelectorAll('.option');
        const optionsArray = Array.from(options);
        for (let i = optionsArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [optionsArray[i], optionsArray[j]] = [optionsArray[j], optionsArray[i]];
        }

        // Add event listeners to options
        options.forEach(option => {
            option.addEventListener('click', () => {
                this.handleAnswer(index, option.dataset.correct === 'true');
                document.body.removeChild(modal);
            });
        });

        document.body.appendChild(modal);
    }

    handleAnswer(questionIndex, isCorrect) {
        const question = this.questions[questionIndex];
        const player = this.players[this.currentPlayer];
        
        if (isCorrect) {
            player.score += question.value;
            alert(`${player.name} answered correctly! +$${question.value}`);
        } else {
            player.score -= question.value;
            alert(`${player.name} answered incorrectly! -$${question.value}\nCorrect answer: ${question.correct_answer}`);
        }

        this.answeredQuestions.add(questionIndex);
        this.currentPlayer = (this.currentPlayer + 1) % this.players.length;
        
        this.updateScoreBoard();
        this.displayQuestions();

        // Check if game is over
        if (this.answeredQuestions.size === this.questions.length) {
            this.endGame();
        }
    }

    updateScoreBoard() {
        const scoresDiv = document.getElementById('scores');
        scoresDiv.innerHTML = '';

        this.players.forEach((player, index) => {
            const playerDiv = document.createElement('div');
            playerDiv.className = 'player-score';
            if (index === this.currentPlayer && this.gameStarted) {
                playerDiv.classList.add('active');
            }
            playerDiv.innerHTML = `
                <strong>${player.name}</strong><br>
                Score: $${player.score}
            `;
            scoresDiv.appendChild(playerDiv);
        });
    }

    endGame() {
        const winner = this.players.reduce((prev, current) => 
            (prev.score > current.score) ? prev : current
        );

        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Game Over!</h2>
                <p>Winner: ${winner.name} with $${winner.score}</p>
                <button onclick="location.reload()">Play Again</button>
            </div>
        `;

        document.body.appendChild(modal);
    }
}

// Initialize game when page loads
document.addEventListener('DOMContentLoaded', () => {
    new JeopardyGame();
});

// Add CSS for modal
const style = document.createElement('style');
style.textContent = `
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        max-width: 600px;
        width: 90%;
        text-align: center;
    }
    
    .question-text {
        font-size: 18px;
        margin: 20px 0;
        line-height: 1.5;
    }
    
    .options {
        display: grid;
        gap: 10px;
        margin: 20px 0;
    }
    
    .option {
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 5px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .option:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    .current-player {
        margin-top: 20px;
        font-weight: bold;
        color: #667eea;
    }
`;
document.head.appendChild(style); 