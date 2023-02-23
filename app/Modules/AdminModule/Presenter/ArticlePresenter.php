<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Article\ArticleForm;
use App\Modules\AdminModule\Component\Article\IArticleFormFactory;
use App\Modules\AdminModule\Component\Article\IArticleGridFactory;
use Ublaboo\DataGrid\DataGrid;

class ArticlePresenter extends Base\BasePresenter
{
    private ?int $id = null;

    /**
     * @param IArticleGridFactory $articleGridFactory
     * @param IArticleFormFactory $articleFormFactory
     */
    public function __construct(
        private IArticleGridFactory $articleGridFactory,
        private IArticleFormFactory $articleFormFactory
    ) {
        parent::__construct();
    }


    public function actionEdit(int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return DataGrid
     */
    public function createComponentArticleGrid(): DataGrid
    {
        return $this->articleGridFactory->create($this)->create();
    }


    /**
     * @return ArticleForm
     */
    public function createComponentArticleForm(): ArticleForm
    {
        return $this->articleFormFactory->create($this->id);
    }
}
