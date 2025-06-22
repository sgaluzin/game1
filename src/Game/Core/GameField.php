<?php

namespace App\Game\Core;

class GameField
{
    private int $size;
    private array $ships = [];
    private array $shots = [];

    public function __construct(int $size = 10)
    {
        $this->size = $size;
    }

    public function addShip(Ship $ship): void
    {
        $this->ships[] = $ship;
    }

    public function getShips(): array
    {
        return $this->ships;
    }

    public function shoot(int $x, int $y): array
    {
        $this->shots[] = [$x, $y];
        foreach ($this->ships as $ship) {
            if ($ship->isHit([$x, $y])) {
                return [
                    'hit' => true,
                    'sunk' => $ship->isSunk(),
                ];
            }
        }
        return ['hit' => false, 'sunk' => false];
    }

    public function getShots(): array
    {
        return $this->shots;
    }

    public function allShipsSunk(): bool
    {
        foreach ($this->ships as $ship) {
            if (!$ship->isSunk()) {
                return false;
            }
        }
        return true;
    }
}

