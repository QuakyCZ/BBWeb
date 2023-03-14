<?php

namespace App\Modules\AdminModule\Component\Rewards;

use App\Component\BaseDataGrid;
use App\Enum\EFlashMessageType;
use App\Repository\Rewards\RewardRepository;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Tracy\Debugger;
use Tracy\ILogger;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\Exception\DataGridException;

class RewardsGrid extends BaseDataGrid
{

    /**
     * @param IContainer $parent
     * @param ITranslator $translator
     * @param RewardRepository $rewardRepository
     */
    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        private RewardRepository $rewardRepository
    )
    {
        parent::__construct($parent, "rewards", $translator);
    }

    /**
     * @return Selection
     */
    protected function getSelection(): Selection
    {
        return $this->rewardRepository->findAll();
    }


    /**
     * @return void
     * @throws DataGridException
     */
    protected function createGrid(): void
    {
        $this->grid->addColumnText(RewardRepository::COLUMN_NAME, 'admin.rewards.field.name');
        $this->grid->addFilterText(RewardRepository::COLUMN_NAME, 'admin.rewards.field.name');

        $this->grid->addColumnText(RewardRepository::COLUMN_COOLDOWN, 'admin.rewards.field.cooldown')
            ->setRenderer(function (ActiveRow $row) {
                return number_format($row[RewardRepository::COLUMN_COOLDOWN], 0, null, ' ') . ' ms';
            })
            ->setAlign('end');

        $this->grid->addAction('edit', '', 'Rewards:edit', ['id' => 'id'])
            ->setClass('btn btn-warning btn-large')
            ->setIcon('pen');

        $this->grid->addActionCallback(
            'delete',
            '',
            function (string $id) {
                try {
                    $this->rewardRepository->setNotDeletedNull($id);
                    $this->grid->presenter->flashMessage('Odměna byla smazána.', EFlashMessageType::WARNING);
                } catch (\PDOException $exception) {
                    Debugger::log($exception, ILogger::EXCEPTION);
                    $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
                }
            }
        )
            ->setConfirmation(new StringConfirmation('Opravdu chcete odstranit tuto odměnu?'))
            ->setClass('btn btn-danger btn-large')
            ->setIcon('trash');


        $this->grid->addToolbarButton(':Admin:Rewards:add', 'admin.rewards.add')
            ->setIcon('plus')
            ->setClass('btn btn-success btn-large');
    }
}

