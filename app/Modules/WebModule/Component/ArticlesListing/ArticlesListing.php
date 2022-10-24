<?php

namespace App\Modules\WebModule\Component\ArticlesListing;

use App\Repository\Primary\ArticleRepository;
use Nette\Application\BadRequestException;

class ArticlesListing extends \App\Component\BaseComponent
{
    public function __construct(
        private int $page,
        private int $maxPerPage,
        private ArticleRepository $articleRepository
    )
    {
    }

    public function render(): void
    {
        $pageCount = 0;
        $this->template->articles = $this->articleRepository
            ->page($this->page + 1, $this->maxPerPage, $pageCount)
            ->order(ArticleRepository::COLUMN_IS_PINNED . ' DESC, '. ArticleRepository::COLUMN_CREATED . ' DESC');
        $this->template->page = $this->page;
        $this->template->pages = $pageCount;
        parent::render(); // TODO: Change the autogenerated stub
    }

    /**
     * @throws BadRequestException
     */
    public function handlePage(int $page): void
    {
        bdump($page);
        if ($page < 0)
        {
            throw new BadRequestException();
        }

        $this->page = $page;
        $pageCount = 0;
        $this->template->articles = $this->articleRepository
            ->page($this->page + 1, $this->maxPerPage, $pageCount)
            ->order(ArticleRepository::COLUMN_IS_PINNED . ' DESC, '. ArticleRepository::COLUMN_CREATED . ' DESC');
        $this->template->page = $this->page;
        $this->template->pages = $pageCount;

        if ($this->presenter->isAjax())
        {
            $this->redrawControl();
            $this->presenter->redrawControl();
        }
        else
        {
            $this->presenter->redirect('Articles:default', [
                'page' => $page
            ]);
        }
    }
}