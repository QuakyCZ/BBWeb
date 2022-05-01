<?php

namespace App\Modules\ApiModule\v1\Handlers\Users;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\UserFacade;
use League\Fractal\ScopeFactoryInterface;
use Nette\Application\AbortException;
use Nette\Http\IResponse;
use Tomaj\NetteApi\Params\PostInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;

class UserConnectMinecraftHandler extends \Tomaj\NetteApi\Handlers\BaseHandler
{
    /**
     * @param UserFacade $userFacade
     * @param ScopeFactoryInterface|null $scopeFactory
     */
    public function __construct
    (
        private UserFacade $userFacade,
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
        return [
            (new PostInputParam('uuid'))->setRequired(),
            (new PostInputParam('nick'))->setRequired()
        ];
    }

    /**
     * @inheritDoc
     */
    public function handle(array $params): ResponseInterface
    {
        if (empty($params))
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'Prázdné tělo dotazu'
            ]);
        }

        $nick = $params['nick'] ?? null;
        $uuid = $params['uuid'] ?? null;

        if ($nick === null || $uuid === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Parametry nick a uuid musí být specifikovány.'
            ]);
        }

        $uuid = str_replace('-', '', $uuid);

        if (!ctype_xdigit($uuid) || strlen($uuid) % 2 !== 0)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Neplatný formát UUID'
            ]);
        }

        if ($this->userFacade->isConnectedToMinecraft($uuid))
        {
            return new JsonApiResponse(200, [
                'status' => 'error',
                'message' => 'Tento účet je již spárován.'
            ]);
        }

        try
        {
            $token = $this->userFacade->createToken(
                EConnectTokenType::MINECRAFT,
                [
                    'uuid' => $uuid,
                    'nick' => $nick
                ]
            );
            return new JsonApiResponse(200, [
                'status' => 'ok',
                'token' => $token
            ]);
        }
        catch (\Throwable $exception)
        {
            Debugger::log($exception);
            return new JsonApiResponse(500, [
                'status' => 'error',
                'message' => 'Nastala neznámá chyba během zpracování požadavku.'
            ]);
        }
    }
}