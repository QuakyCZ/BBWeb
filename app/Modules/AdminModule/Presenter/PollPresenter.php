<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Poll\IPollFormFactory;
use App\Modules\AdminModule\Component\Poll\IPollGridFactory;
use App\Modules\AdminModule\Component\Poll\PollForm;
use App\Modules\AdminModule\Presenter\Base\BasePresenter;
use App\Modules\ClientModule\Component\Poll\IPollResultFactory;
use App\Modules\ClientModule\Component\Poll\PollResult;
use Ublaboo\DataGrid\DataGrid;

class PollPresenter extends BasePresenter
{

    private ?int $id = null;

    public function __construct(
        private IPollFormFactory $votingFormFactory,
        private IPollGridFactory $pollGridFactory,
        private IPollResultFactory $pollResultFactory,
    )
    {
        parent::__construct();
    }

    public function actionEdit(int $id): void
    {
        $this->id = $id;
    }

    public function actionResult(int $id): void
    {
        $this->id = $id;
    }

    public function createComponentPollForm(): PollForm {
        return $this->votingFormFactory->create($this->id);
    }

    public function createComponentPollGrid(): DataGrid {
        return $this->pollGridFactory->create($this)->create();
    }

    public function createComponentPollResult(): PollResult {
        return $this->pollResultFactory->create($this->id);
    }
}