<?php

namespace App\Modules\ClientModule\Component\Poll;

use App\Facade\PollFacade;
use App\Repository\Primary\PollOptionRepository;
use App\Repository\Primary\PollRepository;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;

class PollResult extends \App\Component\BaseComponent
{
    public function __construct(
        private ?int $id,
        private PollFacade $pollFacade
    )
    {
    }

    public function render(): void
    {

        if ($this->id === null) {
            throw new BadRequestException();
        }

        $poll = $this->pollFacade->getPoll($this->id);
        if ($poll === null) {
            throw new BadRequestException();
        }

        $this->template->pollQuestion = $poll[PollRepository::COLUMN_QUESTION];
        $this->template->pollResults = $this->pollFacade->getResults($this->id);
        bdump($this->template->pollResults);

        parent::render(); // TODO: Change the autogenerated stub
    }
}

interface IPollResultFactory {
    /**
     * @param int|null $id
     * @return PollResult
     */
    public function create(?int $id): PollResult;
}