<?php

namespace App\Modules\AdminModule\Component\Poll;

interface IPollFormFactory
{
    /**
     * @param int|null $id
     * @return PollForm
     */
    public function create(?int $id = null): PollForm;
}