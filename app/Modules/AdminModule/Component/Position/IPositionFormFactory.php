<?php

namespace App\Modules\AdminModule\Component\Position;

interface IPositionFormFactory
{
    /**
     * @param int|null $id
     * @return PositionForm
     */
    public function create(?int $id): PositionForm;
}