<?php

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Component\ArticlesListing\ArticlesListing;
use App\Modules\WebModule\Component\ArticlesListing\IArticlesListingFactory;
use App\Repository\Primary\ArticleRepository;
use Michelf\Markdown;
use Michelf\MarkdownExtra;
use Nette\Application\BadRequestException;

class ArticlesPresenter extends Base\BasePresenter
{
    private const ARTICLES_PER_PAGE = 10;
    private int $page = 0;

    /**
     * @param ArticleRepository $articleRepository
     * @param IArticlesListingFactory $articlesListingFactory
     */
    public function __construct(
        private ArticleRepository $articleRepository,
        private IArticlesListingFactory $articlesListingFactory,
    ) {
        parent::__construct();
    }

    public function actionDefault(int $page = 0): void
    {
        $this->page = $page;
    }


    /**
     * @param int $id id clanku
     * @return void
     * @throws BadRequestException
     */
    public function actionShow(int $id): void
    {
        $article = $this->articleRepository->findRow($id);
        if ($article === null) {
            throw new BadRequestException();
        }

        $this->template->article = $article;
        $this->template->textFromMarkdown = MarkdownExtra::defaultTransform($article[ArticleRepository::COLUMN_TEXT]);
    }

    public function createComponentArticlesListing(): ArticlesListing
    {
        return $this->articlesListingFactory->create($this->page);
    }
}
