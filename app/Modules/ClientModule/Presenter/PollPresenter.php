<?php

namespace App\Modules\ClientModule\Presenter;

use App\Facade\PollFacade;
use App\Modules\ClientModule\Component\Poll\IPollResultFactory;
use App\Modules\ClientModule\Component\Poll\IPollVoteFormFactory;
use App\Modules\ClientModule\Component\Poll\PollResult;
use App\Modules\ClientModule\Component\Poll\PollVoteForm;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;

class PollPresenter extends ClientPresenter
{

    private ?int $id = null;

    public function __construct(
        private PollFacade $pollFacade,
        private IPollVoteFormFactory $pollVoteFormFactory,
        private IPollResultFactory $pollResultFactory,
    )
    {
        parent::__construct();
    }

    public function actionDefault(): void {
        $this->template->polls = $this->pollFacade->getPollsForUser($this->user->id);
    }

    /**
     * @throws ForbiddenRequestException
     * @throws BadRequestException
     */
    public function actionVote(int $id): void {
        $this->id = $id;

        if (!$this->pollFacade->isAllowed($id, $this->user->id)) {
            throw new ForbiddenRequestException();
        }

        $poll = $this->pollFacade->getPoll($id);
        if ($poll === null) {
            throw new BadRequestException();
        }

        $this->template->poll = $poll;
        $this->template->hasVoted = $this->pollFacade->hasVoted($id, $this->user->id);

    }

    public function createComponentVoteForm(): PollVoteForm {
        return $this->pollVoteFormFactory->create($this->id);
    }

    public function createComponentPollResult(): PollResult {
        return $this->pollResultFactory->create($this->id);
    }
}