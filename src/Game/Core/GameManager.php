<?php

namespace App\Game\Core;

class GameManager
{
    private GameField $playerField;
    private GameField $computerField;
    private bool $playerTurn;

    public function __construct()
    {
        $this->playerField = new GameField();
        $this->computerField = new GameField();
        $this->playerTurn = true;
    }

    public function getPlayerField(): GameField
    {
        return $this->playerField;
    }

    public function getComputerField(): GameField
    {
        return $this->computerField;
    }

    public function isPlayerTurn(): bool
    {
        return $this->playerTurn;
    }

    public function switchTurn(): void
    {
        $this->playerTurn = !$this->playerTurn;
    }

    public function getRandomPlayerShot(): array
    {
        do {
            $x = rand(0, 9);
            $y = rand(0, 9);
        } while (in_array([$x, $y], $this->playerField->getShots()));
        return [$x, $y];
    }

    public function playerShoot(int $x, int $y): array
    {
        $result = $this->computerField->shoot($x, $y);
        if (!$result['hit']) {
            $this->switchTurn();
        }
        return $result;
    }

    public function computerShoot(?int $x = null, ?int $y = null): array
    {
        if ($x === null || $y === null) {
            [$x, $y] = $this->getRandomPlayerShot();
        }
        $result = $this->playerField->shoot($x, $y);
        if (!$result['hit']) {
            $this->switchTurn();
        }
        return ['x' => $x, 'y' => $y, 'result' => $result];
    }

    public function isGameOver(): bool
    {
        return $this->playerField->allShipsSunk() || $this->computerField->allShipsSunk();
    }
}
