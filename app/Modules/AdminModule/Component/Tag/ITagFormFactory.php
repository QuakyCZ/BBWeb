<?php

namespace App\Modules\AdminModule\Component\Tag;

interface ITagFormFactory
{
    /**
     * @param int|null $id
     * @return TagForm
     */
    public function create(?int $id): TagForm;
}