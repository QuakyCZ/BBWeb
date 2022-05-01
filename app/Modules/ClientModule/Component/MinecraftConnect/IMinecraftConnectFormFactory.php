<?php

namespace App\Modules\ClientModule\Component\MinecraftConnect;

interface IMinecraftConnectFormFactory
{
    /**
     * @return MinecraftConnectForm
     */
    public function create(): MinecraftConnectForm;
}