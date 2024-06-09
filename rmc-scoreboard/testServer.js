// server.js
const express = require('express');
const app = express();
const port = 3000;

let scoreboard = [
  { name: 'Player 1', score: 100 },
  { name: 'Player 2', score: 200 },
  { name: 'Player 3', score: 150 },
];

app.get('/api/scoreboard', (req, res) => {
  res.json(scoreboard);
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});