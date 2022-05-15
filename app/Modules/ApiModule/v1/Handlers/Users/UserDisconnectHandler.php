<?php

namespace App\Modules\ApiModule\v1\Handlers\Users;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\Connector\UserConnectFacadeFactory;
use App\Modules\ApiModule\v1\Enum\EErrorScopeType;
use App\Modules\ApiModule\v1\Handlers\AbstractHandler;
use League\Fractal\ScopeFactoryInterface;
use Nette\Utils\Json;
use Tomaj\NetteApi\Params\JsonInputParam;
use Tomaj\NetteApi\Params\PostInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;
use Tracy\ILogger;

class UserDisconnectHandler extends AbstractHandler
{
    public const PARAM_DATA = 'data';
    public const PARAM_USER_ID = 'user_id';
    public const PARAM_TYPE = 'type';

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
        $schema = [
            'type' => 'object',
            'properties' => [
                self::PARAM_USER_ID => 'int',
                self::PARAM_TYPE => [
                    'type' => 'string',
                    'enum' => EConnectTokenType::getValues()
                ],
            ],
            'required' => [self::PARAM_USER_ID, self::PARAM_TYPE],
        ];
        return [
            (new JsonInputParam(self::PARAM_DATA, Json::encode($schema)))->setRequired()
        ];
    }

    /**
     * @param array $params
     * @return bool|ResponseInterface
     */
    protected function verifyParams(array $params): bool|ResponseInterface
    {
        $userId = $params[self::PARAM_DATA][self::PARAM_USER_ID];
        if (!is_int($userId))
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'scope' => EErrorScopeType::API_ERROR,
                'message' => 'Parametr '. self::PARAM_USER_ID . ' musí být celé číslo'
            ]);
        }

        $type = $params[self::PARAM_DATA][self::PARAM_TYPE];

        if (!EConnectTokenType::hasValue($type))
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'scope' => EErrorScopeType::API_ERROR,
                'message' => 'Neplatný typ propojení.',
            ]);
        }

        return true;
    }

    protected function handleRequest(array $params): ResponseInterface
    {
        $type = $params[self::PARAM_DATA][self::PARAM_TYPE];

        $connector = $this->userConnectFacadeFactory->getInstanceOf($type);

        if ($connector === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'scope' => EErrorScopeType::API_ERROR,
                'message' => 'Neplatný typ propojení'
            ]);
        }

        $userId = $params[self::PARAM_DATA][self::PARAM_USER_ID];

        if (!$connector->isConnected($userId))
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'scope' => EErrorScopeType::LOGICAL_ERROR,
                'message' => 'Tento účet není propojen s ' . ucfirst($type) . ' serverem.'
            ]);
        }

        try
        {
            if ($connector->disconnect($userId))
            {
                return new JsonApiResponse(200, [
                    'status' => 'ok',
                    'message' => 'Účet byl odpojen.'
                ]);
            }
        }
        catch (\Exception $exception)
        {
            Debugger::log($exception, ILogger::EXCEPTION);

            return new JsonApiResponse(500, [
                'status' => 'error',
                'scope' => EErrorScopeType::SERVER_ERROR,
                'message' => 'Při zpracování požadavku nastala neznámá chyba.'
            ]);
        }

        return new JsonApiResponse(404, [
            'status' => 'error',
            'scope' => EErrorScopeType::LOGICAL_ERROR,
            'message' => 'Uživatel nebyl nalezen.'
        ]);
    }
}