<?php

namespace App\Modules\ApiModule\v1\Handlers;

use Tomaj\NetteApi\Handlers\BaseHandler;
use Tomaj\NetteApi\Response\ResponseInterface;

abstract class AbstractHandler extends BaseHandler
{

    /**
     * @inheritDoc
     */
    public function handle(array $params): ResponseInterface
    {
        $verifyResult = $this->verifyParams($params);
        if ($verifyResult !== true)
        {
            return $verifyResult;
        }

        return $this->handleRequest($params);
    }

    /**
     * @param array $params
     * @return bool|ResponseInterface
     */
    protected function verifyParams(array $params): bool|ResponseInterface
    {
        return true;
    }

    abstract protected function handleRequest(array $params): ResponseInterface;
}