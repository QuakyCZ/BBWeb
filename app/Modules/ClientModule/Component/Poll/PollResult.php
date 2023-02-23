<?php

namespace App\Modules\ClientModule\Component\Poll;

use App\Component\BaseComponent;
use App\Facade\PollFacade;
use App\Repository\Primary\PollOptionRepository;
use App\Repository\Primary\PollRepository;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;

class PollResult extends BaseComponent
{
    private ?ActiveRow $poll = null;

    public function __construct(
        private ?int $id,
        protected PollFacade $pollFacade
    ) {
    }

    /**
     * @return void
     * @throws BadRequestException
     */
    public function render(): void
    {
        if ($this->id === null) {
            throw new BadRequestException();
        }

        $poll = $this->getPoll();

        if ($poll === null) {
            throw new BadRequestException();
        }

        $this->template->poll = $poll;
        $results = $this->pollFacade->getResults($this->id);
        $this->template->pollResults = $results;
        $this->template->pollVoters = array_reduce($results, static function ($ax, $dx) {
            return $ax + $dx['votes'];
        }, 0);
        $this->template->participants = $this->pollFacade->getNumberOfAllParticipants($this->id);

        parent::render(); // TODO: Change the autogenerated stub
    }

    protected function getPoll(): ?ActiveRow
    {
        if ($this->poll === null) {
            $this->poll = $this->pollFacade->getPoll($this->id);
        }
        return $this->poll;
    }
}

interface IPollResultFactory
{
    /**
     * @param int|null $id
     * @return PollResult
     */
    public function create(?int $id): PollResult;
}
