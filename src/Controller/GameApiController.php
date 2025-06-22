<?php

namespace App\Controller;

use App\Game\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameApiController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    #[Route('/api/game/start', methods: ['POST'])]
    public function start(): JsonResponse
    {
        $game = $this->gameService->createNewGame();
        $this->gameService->saveGame($game);
        return $this->json(['status' => 'started']);
    }

    #[Route('/api/game/state', methods: ['POST'])]
    public function state(): JsonResponse
    {
        $game = $this->gameService->getGame();
        // Получаем попадания игрока по кораблям компьютера
        $playerHits = [];
        foreach ($game->getComputerField()->getShots() as [$x, $y]) {
            foreach ($game->getComputerField()->getShips() as $ship) {
                if ($ship->isHit([$x, $y])) {
                    $playerHits[] = [$x, $y];
                    break;
                }
            }
        }
        return $this->json([
            'player' => [
                'ships' => $this->serializeShips($game->getPlayerField()->getShips()),
                'shots' => $game->getPlayerField()->getShots(),
            ],
            'computer' => [
                'shots' => $game->getComputerField()->getShots(),
                'hits' => $playerHits,
            ],
            'playerTurn' => $game->isPlayerTurn(),
            'gameOver' => $game->isGameOver(),
        ]);
    }

    #[Route('/api/game/shoot', methods: ['POST'])]
    public function shoot(Request $request): JsonResponse
    {
        $game = $this->gameService->getGame();
        if ($game->isGameOver()) {
            return $this->json(['error' => 'Game over'], 400);
        }
        $data = json_decode($request->getContent(), true);
        $shooter = $data['shooter'] ?? 'player';
        $response = [];
        if ($shooter === 'computer') {
            $comp = $game->computerShoot();
            $this->gameService->saveGame($game);
            $response['computer'] = $comp;
            $response['shooter'] = 'computer';
        } else {
            if (!$game->isPlayerTurn()) {
                return $this->json(['error' => 'Not your turn'], 400);
            }
            $x = $data['x'] ?? null;
            $y = $data['y'] ?? null;
            if ($x === null || $y === null) {
                return $this->json(['error' => 'Invalid coordinates'], 400);
            }
            $result = $game->playerShoot($x, $y);
            $this->gameService->saveGame($game);
            $response['result'] = $result;
            $response['shooter'] = 'player';
        }
        $response['gameOver'] = $game->isGameOver();
        return $this->json($response);
    }

    #[Route('/api/game/reset', methods: ['POST'])]
    public function reset(): JsonResponse
    {
        $this->gameService->resetGame();
        return $this->json(['status' => 'reset']);
    }

    private function serializeShips(array $ships): array
    {
        return array_map(fn($ship) => $ship->getPositions(), $ships);
    }
}
