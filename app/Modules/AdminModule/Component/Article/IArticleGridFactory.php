<?php

namespace App\Modules\AdminModule\Component\Article;

use Nette\ComponentModel\IContainer;

interface IArticleGridFactory
{
    /**
     * @param IContainer $parent
     * @return ArticleGrid
     */
    public function create(IContainer $parent): ArticleGrid;
}