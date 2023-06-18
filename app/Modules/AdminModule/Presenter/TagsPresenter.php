<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Tag\ITagFormFactory;
use App\Modules\AdminModule\Component\Tag\ITagGridFactory;
use App\Modules\AdminModule\Component\Tag\TagForm;
use Ublaboo\DataGrid\DataGrid;

class TagsPresenter extends Base\BasePresenter
{

    private ?int $id = null;

    public function __construct(
        private ITagFormFactory $tagFormFactory,
        private ITagGridFactory $tagGridFactory,
    )
    {
        parent::__construct();
    }


    public function actionEdit(int $id): void {
        $this->id = $id;
    }

    public function createComponentTagGrid(): DataGrid {
        return $this->tagGridFactory->create($this)->create();
    }

    public function createComponentTagForm(): TagForm {
        return $this->tagFormFactory->create($this->id);
    }
}