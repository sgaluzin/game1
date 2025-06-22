<?php

namespace App\Game\Core;

class Ship
{
    private array $positions;
    private array $hits;

    public function __construct(array $positions)
    {
        $this->positions = $positions;
        $this->hits = [];
    }

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function isHit(array $shot): bool
    {
        foreach ($this->positions as $pos) {
            if ($pos[0] === $shot[0] && $pos[1] === $shot[1]) {
                $this->hits[] = $shot;
                return true;
            }
        }
        return false;
    }

    public function isSunk(): bool
    {
        foreach ($this->positions as $pos) {
            if (!in_array($pos, $this->hits)) {
                return false;
            }
        }
        return true;
    }
}

