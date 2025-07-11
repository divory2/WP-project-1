const { useState, useEffect } = React;

// Main App Component
function App() {
  const [currentPage, setCurrentPage] = useState('home');
  const [gameSettings, setGameSettings] = useState(null);

  const navigateTo = (page, settings = null) => {
    setCurrentPage(page);
    if (settings) {
      setGameSettings(settings);
    }
  };

  const renderPage = () => {
    switch (currentPage) {
      case 'home':
        return <Home onStartGame={navigateTo} />;
      case 'game':
        return <Game settings={gameSettings} onGameEnd={navigateTo} />;
      case 'leaderboard':
        return <Leaderboard onBackToHome={() => navigateTo('home')} />;
      default:
        return <Home onStartGame={navigateTo} />;
    }
  };

  return (
    <div className="App">
      {renderPage()}
    </div>
  );
}

// Home Component
function Home({ onStartGame }) {
  const [categories, setCategories] = useState([]);
  const [difficulties, setDifficulties] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('');
  const [selectedDifficulty, setSelectedDifficulty] = useState('');
  const [playerNames, setPlayerNames] = useState(['', '', '']);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [categoriesRes, difficultiesRes] = await Promise.all([
          axios.get('api.php?endpoint=categories'),
          axios.get('api.php?endpoint=difficulties')
        ]);

        setCategories(categoriesRes.data.trivia_categories || []);
        setDifficulties(difficultiesRes.data.difficulties || []);
        
        // Set defaults
        if (categoriesRes.data.trivia_categories?.length > 0) {
          setSelectedCategory(categoriesRes.data.trivia_categories[0].id.toString());
        }
        if (difficultiesRes.data.difficulties?.length > 0) {
          setSelectedDifficulty(difficultiesRes.data.difficulties[0].id);
        }
      } catch (err) {
        setError('Failed to load game settings. Please try again.');
        console.error('Error fetching data:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const handlePlayerNameChange = (index, value) => {
    const newNames = [...playerNames];
    newNames[index] = value;
    setPlayerNames(newNames);
  };

  const handleStartGame = () => {
    if (!selectedCategory || !selectedDifficulty) {
      setError('Please select both category and difficulty.');
      return;
    }

    const validNames = playerNames.filter(name => name.trim() !== '');
    if (validNames.length < 2) {
      setError('Please enter at least 2 player names.');
      return;
    }

    const settings = {
      category: selectedCategory,
      difficulty: selectedDifficulty,
      playerNames: validNames
    };

    onStartGame('game', settings);
  };

  if (loading) {
    return (
      <div className="container">
        <div className="loading">Loading game settings...</div>
      </div>
    );
  }

  return (
    <div className="container">
      <div className="card">
        <h1 style={{ textAlign: 'center', marginBottom: '30px', fontSize: '2.5rem', color: '#333' }}>
          🎯 Jeopardy Game
        </h1>

        {error && <div className="error">{error}</div>}

        <div className="form-group">
          <label htmlFor="category">Select Category:</label>
          <select
            id="category"
            className="form-control"
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
          >
            {categories.map(category => (
              <option key={category.id} value={category.id}>
                {category.name}
              </option>
            ))}
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="difficulty">Select Difficulty:</label>
          <select
            id="difficulty"
            className="form-control"
            value={selectedDifficulty}
            onChange={(e) => setSelectedDifficulty(e.target.value)}
          >
            {difficulties.map(difficulty => (
              <option key={difficulty.id} value={difficulty.id}>
                {difficulty.name}
              </option>
            ))}
          </select>
        </div>

        <div className="form-group">
          <label>Player Names (3 players):</label>
          {playerNames.map((name, index) => (
            <input
              key={index}
              type="text"
              className="form-control"
              placeholder={`Player ${index + 1} Name`}
              value={name}
              onChange={(e) => handlePlayerNameChange(index, e.target.value)}
              style={{ marginBottom: '10px' }}
            />
          ))}
        </div>

        <div style={{ textAlign: 'center', marginTop: '30px' }}>
          <button className="btn" onClick={handleStartGame}>
            Start Game
          </button>
          <button 
            className="btn btn-secondary" 
            onClick={() => onStartGame('leaderboard')}
            style={{ marginLeft: '15px' }}
          >
            View Leaderboard
          </button>
        </div>
      </div>
    </div>
  );
}

// Game Component
function Game({ settings, onGameEnd }) {
  const [questions, setQuestions] = useState([]);
  const [currentQuestion, setCurrentQuestion] = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [selectedAnswer, setSelectedAnswer] = useState(null);
  const [answeredQuestions, setAnsweredQuestions] = useState(new Set());
  const [currentPlayer, setCurrentPlayer] = useState(0);
  const [players, setPlayers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [gameEnded, setGameEnded] = useState(false);

  useEffect(() => {
    const initializeGame = async () => {
      try {
        const response = await axios.get('api.php', {
          params: {
            endpoint: 'questions',
            category: settings.category,
            difficulty: settings.difficulty,
            amount: 15 // 5 questions per player
          }
        });

        if (response.data.response_code === 0) {
          setQuestions(response.data.results);
          
          // Initialize players
          const initialPlayers = settings.playerNames.map((name, index) => ({
            id: index,
            name: name,
            score: 0,
            questionsAnswered: 0
          }));
          setPlayers(initialPlayers);
        } else {
          setError('Failed to load questions. Please try again.');
        }
      } catch (err) {
        setError('Failed to load questions. Please try again.');
        console.error('Error fetching questions:', err);
      } finally {
        setLoading(false);
      }
    };

    if (settings) {
      initializeGame();
    }
  }, [settings]);

  const handleQuestionClick = (questionIndex) => {
    if (answeredQuestions.has(questionIndex)) return;
    
    const question = questions[questionIndex];
    setCurrentQuestion({ ...question, index: questionIndex });
    setSelectedAnswer(null);
    setShowModal(true);
  };

  const handleAnswerSelect = (answer) => {
    setSelectedAnswer(answer);
  };

  const handleSubmitAnswer = () => {
    if (!selectedAnswer) return;

    const isCorrect = selectedAnswer === currentQuestion.correct_answer;
    const points = getPointsForDifficulty(settings.difficulty);
    
    // Update player score
    const updatedPlayers = [...players];
    if (isCorrect) {
      updatedPlayers[currentPlayer].score += points;
    } else {
      updatedPlayers[currentPlayer].score = Math.max(0, updatedPlayers[currentPlayer].score - points);
    }
    updatedPlayers[currentPlayer].questionsAnswered += 1;
    setPlayers(updatedPlayers);

    // Mark question as answered
    setAnsweredQuestions(prev => new Set([...prev, currentQuestion.index]));

    // Check if game is over
    const totalAnswered = answeredQuestions.size + 1;
    if (totalAnswered >= questions.length) {
      endGame(updatedPlayers);
      return;
    }

    // Move to next player
    setCurrentPlayer((prev) => (prev + 1) % players.length);
    setShowModal(false);
  };

  const getPointsForDifficulty = (difficulty) => {
    switch (difficulty) {
      case 'easy': return 100;
      case 'medium': return 200;
      case 'hard': return 300;
      default: return 100;
    }
  };

  const endGame = (finalPlayers) => {
    setGameEnded(true);
    
    // Save to leaderboard
    const leaderboard = JSON.parse(localStorage.getItem('jeopardyLeaderboard') || '[]');
    const gameResult = {
      date: new Date().toISOString(),
      category: settings.category,
      difficulty: settings.difficulty,
      players: finalPlayers.map(player => ({
        name: player.name,
        score: player.score
      }))
    };
    
    leaderboard.push(gameResult);
    leaderboard.sort((a, b) => {
      const aMaxScore = Math.max(...a.players.map(p => p.score));
      const bMaxScore = Math.max(...b.players.map(p => p.score));
      return bMaxScore - aMaxScore;
    });
    
    // Keep only top 10 games
    const topLeaderboard = leaderboard.slice(0, 10);
    localStorage.setItem('jeopardyLeaderboard', JSON.stringify(topLeaderboard));
  };

  const renderGameBoard = () => {
    const points = [100, 200, 300, 400, 500];
    const categories = ['Category 1', 'Category 2', 'Category 3'];
    
    return (
      <div className="game-board">
        {points.map((point, rowIndex) => (
          <React.Fragment key={point}>
            {categories.map((category, colIndex) => {
              const questionIndex = rowIndex * 3 + colIndex;
              const isAnswered = answeredQuestions.has(questionIndex);
              
              return (
                <div
                  key={`${point}-${category}`}
                  className={`question-card ${isAnswered ? 'answered' : ''}`}
                  onClick={() => handleQuestionClick(questionIndex)}
                >
                  {isAnswered ? '✓' : `$${point}`}
                </div>
              );
            })}
          </React.Fragment>
        ))}
      </div>
    );
  };

  const renderPlayers = () => {
    return (
      <div className="players">
        {players.map((player, index) => (
          <div
            key={player.id}
            className={`player-card ${index === currentPlayer && !gameEnded ? 'active' : ''}`}
          >
            <h3>{player.name}</h3>
            <div className="player-score">${player.score}</div>
            <div>Questions: {player.questionsAnswered}/5</div>
          </div>
        ))}
      </div>
    );
  };

  const renderQuestionModal = () => {
    if (!currentQuestion) return null;

    const allAnswers = [...currentQuestion.incorrect_answers, currentQuestion.correct_answer];
    const shuffledAnswers = allAnswers.sort(() => Math.random() - 0.5);

    return (
      <div className="modal">
        <div className="modal-content">
          <h2>Question</h2>
          <p style={{ marginBottom: '20px', fontSize: '18px' }}>
            {currentQuestion.question}
          </p>
          
          <div className="answer-options">
            {shuffledAnswers.map((answer, index) => (
              <div
                key={index}
                className={`answer-option ${selectedAnswer === answer ? 'selected' : ''}`}
                onClick={() => handleAnswerSelect(answer)}
              >
                {answer}
              </div>
            ))}
          </div>
          
          <div style={{ textAlign: 'center', marginTop: '20px' }}>
            <button
              className="btn"
              onClick={handleSubmitAnswer}
              disabled={!selectedAnswer}
            >
              Submit Answer
            </button>
            <button
              className="btn btn-secondary"
              onClick={() => setShowModal(false)}
              style={{ marginLeft: '10px' }}
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    );
  };

  const renderGameEnd = () => {
    const winner = players.reduce((prev, current) => 
      (prev.score > current.score) ? prev : current
    );

    return (
      <div className="modal">
        <div className="modal-content">
          <h2>Game Over!</h2>
          <div style={{ textAlign: 'center', margin: '20px 0' }}>
            <h3>🏆 Winner: {winner.name} 🏆</h3>
            <p>Final Score: ${winner.score}</p>
          </div>
          
          <div style={{ marginBottom: '20px' }}>
            <h4>Final Standings:</h4>
            {players
              .sort((a, b) => b.score - a.score)
              .map((player, index) => (
                <div key={player.id} className="leaderboard-item">
                  <span className="leaderboard-rank">#{index + 1}</span>
                  <span>{player.name}</span>
                  <span className="leaderboard-score">${player.score}</span>
                </div>
              ))
            }
          </div>
          
          <div style={{ textAlign: 'center' }}>
            <button className="btn" onClick={() => onGameEnd('home')}>
              Play Again
            </button>
            <button
              className="btn btn-secondary"
              onClick={() => onGameEnd('leaderboard')}
              style={{ marginLeft: '10px' }}
            >
              View Leaderboard
            </button>
          </div>
        </div>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="container">
        <div className="loading">Loading game...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="container">
        <div className="card">
          <div className="error">{error}</div>
          <button className="btn" onClick={() => onGameEnd('home')}>
            Back to Home
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="container">
      <div className="card">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
          <h1>🎯 Jeopardy Game</h1>
          <button className="btn btn-secondary" onClick={() => onGameEnd('home')}>
            Back to Home
          </button>
        </div>

        {renderPlayers()}
        {renderGameBoard()}
      </div>

      {showModal && renderQuestionModal()}
      {gameEnded && renderGameEnd()}
    </div>
  );
}

// Leaderboard Component
function Leaderboard({ onBackToHome }) {
  const [leaderboard, setLeaderboard] = useState([]);

  useEffect(() => {
    const savedLeaderboard = JSON.parse(localStorage.getItem('jeopardyLeaderboard') || '[]');
    setLeaderboard(savedLeaderboard);
  }, []);

  const clearLeaderboard = () => {
    if (window.confirm('Are you sure you want to clear the leaderboard? This action cannot be undone.')) {
      localStorage.removeItem('jeopardyLeaderboard');
      setLeaderboard([]);
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
  };

  const getDifficultyColor = (difficulty) => {
    switch (difficulty) {
      case 'easy': return '#28a745';
      case 'medium': return '#ffc107';
      case 'hard': return '#dc3545';
      default: return '#6c757d';
    }
  };

  if (leaderboard.length === 0) {
    return (
      <div className="container">
        <div className="card">
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
            <h1>🏆 Leaderboard</h1>
            <button className="btn btn-secondary" onClick={onBackToHome}>
              Back to Home
            </button>
          </div>
          
          <div style={{ textAlign: 'center', padding: '40px' }}>
            <h3>No games played yet!</h3>
            <p style={{ color: '#666', marginTop: '10px' }}>
              Play a game to see your scores here.
            </p>
            <button className="btn" onClick={onBackToHome} style={{ marginTop: '20px' }}>
              Start Playing
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="container">
      <div className="card">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
          <h1>🏆 Leaderboard</h1>
          <div>
            <button className="btn btn-danger" onClick={clearLeaderboard} style={{ marginRight: '10px' }}>
              Clear Leaderboard
            </button>
            <button className="btn btn-secondary" onClick={onBackToHome}>
              Back to Home
            </button>
          </div>
        </div>

        <div className="leaderboard">
          {leaderboard.map((game, gameIndex) => {
            const winner = game.players.reduce((prev, current) => 
              (prev.score > current.score) ? prev : current
            );
            
            return (
              <div key={gameIndex} style={{ marginBottom: '30px', border: '1px solid #e9ecef', borderRadius: '12px', padding: '20px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '15px' }}>
                  <h3>Game #{gameIndex + 1}</h3>
                  <div style={{ textAlign: 'right' }}>
                    <div style={{ fontSize: '14px', color: '#666' }}>
                      {formatDate(game.date)}
                    </div>
                    <div style={{ 
                      display: 'inline-block', 
                      padding: '4px 8px', 
                      borderRadius: '4px', 
                      fontSize: '12px', 
                      fontWeight: 'bold',
                      color: 'white',
                      backgroundColor: getDifficultyColor(game.difficulty)
                    }}>
                      {game.difficulty.toUpperCase()}
                    </div>
                  </div>
                </div>
                
                <div style={{ marginBottom: '15px' }}>
                  <strong>Winner:</strong> {winner.name} (${winner.score})
                </div>
                
                <div>
                  <strong>Final Standings:</strong>
                  {game.players
                    .sort((a, b) => b.score - a.score)
                    .map((player, playerIndex) => (
                      <div key={playerIndex} className="leaderboard-item">
                        <span className="leaderboard-rank">#{playerIndex + 1}</span>
                        <span>{player.name}</span>
                        <span className="leaderboard-score">${player.score}</span>
                      </div>
                    ))
                  }
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}

// Render the app
ReactDOM.render(<App />, document.getElementById('app')); 