<?php

namespace App\Modules\ApiModule\v1\Handlers\Users;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\Connector\UserConnectFacadeFactory;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\UserConnectTokenRepository;
use League\Fractal\ScopeFactoryInterface;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use Nette\Utils\Json;
use Tomaj\NetteApi\Params\JsonInputParam;
use Tomaj\NetteApi\Params\PostInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;

class UserConnectHandler extends \Tomaj\NetteApi\Handlers\BaseHandler
{

    public const PARAM_TYPE = 'type';
    public const PARAM_TOKEN = 'token';
    public const PARAM_DATA = 'data';

    /**
     * @param UserFacade $userFacade
     * @param UserConnectFacadeFactory $userConnectFacadeFactory
     * @param ScopeFactoryInterface|null $scopeFactory
     */
    public function __construct
    (
        private UserFacade $userFacade,
        private UserConnectFacadeFactory $userConnectFacadeFactory,
        ScopeFactoryInterface $scopeFactory = null,
    )
    {
        parent::__construct($scopeFactory);
    }

    /**
     * @inheritDoc
     */
    public function params(): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                self::PARAM_TYPE => [
                    'type' => 'string',
                    'enum' => EConnectTokenType::getValues()
                ],
                self::PARAM_TOKEN => 'string',
                self::PARAM_DATA => 'object'
            ],
            'required' => [self::PARAM_TYPE, self::PARAM_TOKEN, self::PARAM_DATA],
            'additionalProperties' => true
        ];
        return [
            (new JsonInputParam(self::PARAM_DATA, Json::encode($schema)))->setRequired()
        ];
    }

    /**
     * @inheritDoc
     */
    public function handle(array $params): ResponseInterface
    {
        $type = $params[self::PARAM_DATA][self::PARAM_TYPE];
        $token = $params[self::PARAM_DATA][self::PARAM_TOKEN];
        $data = $params[self::PARAM_DATA][self::PARAM_DATA];

        $connector = $this->userConnectFacadeFactory->getInstanceOf($type);
        if ($connector === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Neznámý typ propojení'
            ]);
        }

        $token = $connector->validateToken($token);

        if ($token === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Neplatný token'
            ]);
        }

        return $connector->connect($token[UserConnectTokenRepository::COLUMN_USER_ID], $data);
    }
}