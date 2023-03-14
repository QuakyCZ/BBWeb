<?php

namespace App\Modules\AdminModule\Component\Rewards;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Facade\RewardsFacade;
use App\Repository\Primary\PollOptionRepository;
use App\Repository\Rewards\RewardCommandRepository;
use App\Repository\Rewards\RewardPermissionRepository;
use App\Repository\Rewards\RewardRepository;
use App\Repository\Rewards\ServerRepository;
use Contributte\FormMultiplier\Multiplier;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Forms\Container;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class RewardsForm extends BaseComponent
{
    public function __construct(
        private ?int $id,
        private RewardsFacade $rewardsFacade,
        private RewardRepository $rewardRepository,
        private ServerRepository $serverRepository,
        private Translator $translator
    )
    {
    }

    /**
     * @return void
     * @throws BadRequestException
     */
    public function render(): void
    {

        if ($this->id !== null) {
            $formValues = $this->rewardsFacade->getFormValues($this->id);

            if ($formValues === null)
                throw new BadRequestException();

            /** @var Form $form */
            $form = $this['form'];
            $form->setDefaults($formValues);
        }

        parent::render();
    }


    public function createComponentForm(): Form {
        $form = new Form();
        $form->getElementPrototype()->class("ajax");

        // name

        $form->addText(
            RewardRepository::COLUMN_NAME,
            $this->translator->translate('admin.rewards.field.name')
        )
            ->setRequired();

        // cooldown

        $form->addInteger(
            RewardRepository::COLUMN_COOLDOWN,
            $this->translator->translate('admin.rewards.field.cooldown')
        )
            ->setDefaultValue(10000)
            ->setRequired();

        // servers

        $form->addMultiSelect(
            "server_ids",
            $this->translator->translate('admin.rewards.field.servers'),
            $this->serverRepository->fetchForChoiceControl()
        );

        // permissions

        /** @var Multiplier $permissionMultiplier */
        $permissionMultiplier = $form->addMultiplier('permissions', function (Container $container) {
            $container->addText(
                RewardPermissionRepository::COLUMN_PERMISSION,
                $this->translator->translate('admin.rewards.field.permission')
            )->setRequired('Toto pole je povinné');
        }, 0);

        $permissionMultiplier->addCreateButton(
            $this->translator->translate('admin.rewards.field.addPermission')
        )
            ->addClass('btn btn-success');

        $permissionMultiplier->addRemoveButton(
            $this->translator->translate('admin.remove')
        )
            ->addClass('btn btn-danger');

        // commands

        /** @var Multiplier $commandsMultiplier */
        $commandsMultiplier = $form->addMultiplier('commands', function (Container $container) {
            $container->addText(
                RewardCommandRepository::COLUMN_COMMAND,
                $this->translator->translate('admin.rewards.field.command')
            )
                ->setRequired('Toto pole je povinné');
            $container->addInteger(
                RewardCommandRepository::COLUMN_ORDER,
                $this->translator->translate('admin.rewards.field.order')
            )
                ->setDefaultValue(0)
                ->setRequired();
        }, 0);

        $commandsMultiplier->addCreateButton(
            $this->translator->translate('admin.rewards.field.addCommand')
        )
            ->addClass('btn btn-success');

        $commandsMultiplier->addRemoveButton(
            $this->translator->translate('admin.remove')
        )->addClass('btn btn-danger');


        $form->addSubmit(
            'save',
            $this->translator->translate('admin.save')
        );

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onAnchor[] = [$this, 'onAnchor'];

        return $form;
    }


    /**
     * @param Form $form
     * @return void
     */
    public function onAnchor(Form $form): void {
        if ($this->presenter->isAjax()) {
            $this->redrawControl();
        }
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     */
    public function saveForm(Form $form): void {
        /** @var RewardsFormValues $values */
        $values = $form->getValues(RewardsFormValues::class);

        try {
            $this->rewardsFacade->saveReward($values, $this->id);
            $this->presenter->flashMessage("Odměna byla přidána.", EFlashMessageType::SUCCESS);
            $this->presenter->redirect('Rewards:');
        } catch (UniqueConstraintViolationException) {
            $form->addError("Odměna s tímto názvem již existuje.");
        } catch (\PDOException $exception) {
            $form->addError("Během zpracování požadavku nastala chyba.");
            Debugger::log($exception, ILogger::EXCEPTION);
        }
    }
}