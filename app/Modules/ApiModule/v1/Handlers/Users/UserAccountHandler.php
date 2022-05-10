<?php

namespace App\Modules\ApiModule\v1\Handlers\Users;

use App\Modules\ApiModule\Model\User\Connector\UserConnectFacadeFactory;
use App\Utils\MinecraftUtils;
use League\Fractal\ScopeFactoryInterface;
use Tomaj\NetteApi\Handlers\BaseHandler;
use Tomaj\NetteApi\Params\GetInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;

class UserAccountHandler extends BaseHandler
{

    /**
     * @param UserConnectFacadeFactory $userConnectFacadeFactory
     * @param ScopeFactoryInterface|null $scopeFactory
     */
    public function __construct
    (
        private UserConnectFacadeFactory $userConnectFacadeFactory,
        ScopeFactoryInterface $scopeFactory = null
    )
    {
        parent::__construct($scopeFactory);
    }

    public function params(): array
    {
        return [
            (new GetInputParam('user_id'))->setRequired(),
            (new GetInputParam('type'))->setRequired()
        ];
    }

    /**
     * @inheritDoc
     */
    public function handle(array $params): ResponseInterface
    {
        $userId = $params['user_id'];

        if (!ctype_digit($userId))
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'user_id musí být celé číslo'
            ]);
        }

        $connector = $this->userConnectFacadeFactory->getInstanceOf($params['type']);
        if ($connector === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Neexistující typ propojení'
            ]);
        }

        $account = $connector->getAccount($userId);
        if ($account === null)
        {
            return new JsonApiResponse(404, [
                'status' => 'error',
                'message' => 'Účet nebyl nalezen.'
            ]);
        }

        return new JsonApiResponse(200, [
            'status' => 'ok',
            'account' => [
                'nick' => $account['nick'],
                'uuid' => MinecraftUtils::bin2uuid($account['uuid'])
            ]
        ]);
    }
}