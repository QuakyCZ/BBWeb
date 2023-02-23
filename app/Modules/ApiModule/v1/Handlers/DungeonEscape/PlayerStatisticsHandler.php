<?php

namespace App\Modules\ApiModule\v1\Handlers\DungeonEscape;

use App\Modules\ApiModule\Model\PlayerStatistics\PlayerStatisticsFacade;
use League\Fractal\ScopeFactoryInterface;
use Tomaj\NetteApi\Params\GetInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;

class PlayerStatisticsHandler extends \Tomaj\NetteApi\Handlers\BaseHandler
{
    public const PARAM_ID = 'id';

    public function __construct(
        private PlayerStatisticsFacade $playerStatisticsFacade,
        ScopeFactoryInterface $scopeFactory = null
    ) {
        parent::__construct($scopeFactory);
    }

    public function params(): array
    {
        return [
            (new GetInputParam(self::PARAM_ID))->setRequired()
        ];
    }

    /**
     * @inheritDoc
     */
    public function handle(array $params): ResponseInterface
    {
        $id = $params[self::PARAM_ID];

        $stats = $this->playerStatisticsFacade->getPlayerStatistics($id);
        if ($stats === null) {
            return new JsonApiResponse(404, [
                'status' => 'error',
                'message' => 'Statistiky s požadovaným ID nebyly nalezeny.'
            ]);
        }
        return new JsonApiResponse(200, [
            'status' => 'ok',
            'statistics' => $stats->toArray()
        ]);
    }
}
