document.addEventListener('DOMContentLoaded', function () {
    const playerField = document.getElementById('player-field');
    const computerField = document.getElementById('computer-field');
    const status = document.getElementById('status');
    const startBtn = document.getElementById('start-btn');
    const resetBtn = document.getElementById('reset-btn');
    let gameOver = false;

    function createField(table, clickable) {
        table.innerHTML = '';
        for (let y = 0; y < 10; y++) {
            const row = document.createElement('tr');
            for (let x = 0; x < 10; x++) {
                const cell = document.createElement('td');
                cell.dataset.x = x;
                cell.dataset.y = y;
                if (clickable) {
                    cell.addEventListener('click', () => shoot(x, y));
                }
                row.appendChild(cell);
            }
            table.appendChild(row);
        }
    }

    function updateFields(state) {
        // Ваше поле
        for (let y = 0; y < 10; y++) {
            for (let x = 0; x < 10; x++) {
                const cell = playerField.rows[y].cells[x];
                cell.className = '';
            }
        }
        // Отображаем корабли игрока
        if (state.player && state.player.ships) {
            state.player.ships.forEach(ship => {
                ship.forEach(([x, y]) => {
                    playerField.rows[y].cells[x].classList.add('ship');
                });
            });
        }
        if (state.player && state.player.shots) {
            state.player.shots.forEach(([x, y]) => {
                playerField.rows[y].cells[x].classList.add('shot');
            });
        }
        // Поле компьютера
        for (let y = 0; y < 10; y++) {
            for (let x = 0; x < 10; x++) {
                const cell = computerField.rows[y].cells[x];
                cell.className = '';
            }
        }
        if (state.computer && state.computer.shots) {
            state.computer.shots.forEach(([x, y]) => {
                computerField.rows[y].cells[x].classList.add('shot');
            });
        }
        // Отметить попадания игрока по кораблям компьютера
        if (state.computer && state.computer.hits) {
            state.computer.hits.forEach(([x, y]) => {
                computerField.rows[y].cells[x].classList.add('hit');
            });
        }
        // (Опционально) показать корабли компьютера, если игра окончена
        if (state.gameOver && state.computer && state.computer.ships) {
            state.computer.ships.forEach(ship => {
                ship.forEach(([x, y]) => {
                    computerField.rows[y].cells[x].classList.add('ship');
                });
            });
        }
    }

    function setStatus(text) {
        status.textContent = text;
    }

    function loadState() {
        fetch('/api/game/state', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
            .then(r => r.json())
            .then(state => {
                updateFields(state);
                gameOver = state.gameOver;
                if (state.gameOver) {
                    setStatus('Игра окончена!');
                } else if (state.playerTurn) {
                    setStatus('Ваш ход');
                } else {
                    setStatus('Ход компьютера...');
                    // Если ход компьютера, имитируем его выстрел
                    setTimeout(() => {
                        fetch('/api/game/shoot', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({shooter: 'computer'}) // явно указываем, что стреляет компьютер
                        })
                            .then(r => r.json())
                            .then(() => loadState());
                    }, 700);
                }
            });
    }

    function shoot(x, y) {
        if (gameOver) return;
        fetch('/api/game/shoot', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({x, y})
        })
            .then(r => r.json())
            .then(data => {
                if (data && data.shooter === 'player' && data.result) {
                    if (data.result.hit) {
                        setStatus('Попадание!');
                    } else {
                        setStatus('Мимо!');
                    }
                }
                loadState();
                // Если после выстрела ход компьютера, ждем и обновляем поле
                if (data && data.computer) {
                    setTimeout(loadState, 700);
                }
            });
    }

    startBtn.onclick = () => {
        fetch('/api/game/start', {method: 'POST'})
            .then(() => loadState());
    };
    resetBtn.onclick = () => {
        fetch('/api/game/reset', {method: 'POST'})
            .then(() => loadState());
    };

    createField(playerField, false);
    createField(computerField, true);
    loadState();
});
