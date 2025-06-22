<?php

namespace App\Game;

use App\Game\Core\GameField;
use App\Game\Core\GameManager;
use App\Game\Core\Ship;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GameService
{
    private SessionInterface $session;
    private const SESSION_KEY = 'battleship_game';

    public function __construct(private RequestStack $requestStack,)
    {
        $this->session = $requestStack->getSession();
    }

    public function getGame(): GameManager
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            $game = $this->createNewGame();
            $this->saveGame($game);
            return $game;
        }
        return unserialize($this->session->get(self::SESSION_KEY));
    }

    public function saveGame(GameManager $game): void
    {
        $this->session->set(self::SESSION_KEY, serialize($game));
    }

    public function createNewGame(): GameManager
    {
        $game = new GameManager();
        // Расставляем корабли случайно (упрощённо)
        $this->placeShips($game->getPlayerField());
        $this->placeShips($game->getComputerField());
        return $game;
    }

    private function placeShips(GameField $field): void
    {
        // Пример: 1 корабль на 4 клетки, 2 на 3 клетки, 3 на 2 клетки, 4 на 1 клетку
        $ships = [4, 3, 3, 2, 2, 2, 1, 1, 1, 1];
        foreach ($ships as $size) {
            do {
                $horizontal = rand(0, 1) === 1;
                $x = rand(0, $horizontal ? 10 - $size : 9);
                $y = rand(0, $horizontal ? 9 : 10 - $size);
                $positions = [];
                for ($i = 0; $i < $size; $i++) {
                    $positions[] = [$x + ($horizontal ? $i : 0), $y + ($horizontal ? 0 : $i)];
                }
            } while ($this->overlaps($positions, $field->getShips()));
            $field->addShip(new Ship($positions));
        }
    }

    private function overlaps(array $positions, array $ships): bool
    {
        foreach ($ships as $ship) {
            foreach ($ship->getPositions() as $pos) {
                foreach ($positions as $p) {
                    if ($p[0] === $pos[0] && $p[1] === $pos[1]) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function resetGame(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }
}

