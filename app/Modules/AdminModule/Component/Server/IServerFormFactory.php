<?php

namespace App\Modules\AdminModule\Component\Server;

interface IServerFormFactory
{
    /**
     * @param int|null $id
     * @return ServerForm
     */
    public function create(?int $id = null): ServerForm;
}