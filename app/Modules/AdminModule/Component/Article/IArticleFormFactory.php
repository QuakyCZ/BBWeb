<?php

namespace App\Modules\AdminModule\Component\Article;

interface IArticleFormFactory
{
    /**
     * @param int|null $id
     * @return ArticleForm
     */
    public function create(?int $id = null): ArticleForm;
}