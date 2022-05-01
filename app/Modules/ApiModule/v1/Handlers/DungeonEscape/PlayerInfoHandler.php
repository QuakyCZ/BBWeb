<?php

namespace App\Modules\ApiModule\v1\Handlers\DungeonEscape;

use App\Modules\ApiModule\Model\Player\PlayerFacade;
use App\Modules\ApiModule\Model\PlayerStatistics\PlayerStatisticsFacade;
use App\Repository\DungeonEscape\PlayerRepository;
use League\Fractal\ScopeFactoryInterface;
use Tomaj\NetteApi\Handlers\BaseHandler;
use Tomaj\NetteApi\Params\GetInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;

class PlayerInfoHandler extends BaseHandler
{
    public function __construct
    (
        ScopeFactoryInterface $scopeFactory = null,
        private PlayerFacade $playerFacade,
    )
    {
        parent::__construct($scopeFactory);
    }

    public function params(): array
    {
        return [
            new GetInputParam('name'),
            new GetInputParam('uuid')
        ];
    }

    /**
     * @inheritDoc
     */
    public function handle(array $params): ResponseInterface
    {
        $player = null;
        $uuid = $params['uuid'] ?? null;
        $name = $params['name'] ?? null;
        if ($uuid === null && $name === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'UUID or Name must be specified.',
            ]);
        }
        if ($uuid !== null)
        {
            $player = $this->playerFacade->getByUuid($uuid);
        }
        else
        {
            $player = $this->playerFacade->getByName($name);
        }

        return new JsonApiResponse(200, [
            'status' => 'ok',
            'player' => $player->toArray()
        ]);
    }
}