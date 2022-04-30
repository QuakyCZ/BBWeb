<?php

namespace App\Modules\AdminModule\Component\Settings;

use App\Component\BaseComponent;
use App\Repository\Primary\SettingsRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class SettingsForm extends BaseComponent
{

    private ?int $id;

    private SettingsRepository $settingRepository;

    public function __construct
    (
        ?int               $id,
        SettingsRepository $settingRepository,
        private Translator $translator,
    )
    {
        $this->id = $id;
        $this->settingRepository = $settingRepository;
    }

    /**
     * @throws BadRequestException
     */
    public function render(): void
    {

        if ($this->id !== null)
        {

            $row = $this->settingRepository->get($this->id);

            if ($row === null)
            {
                throw new BadRequestException('admin.settings.error.not_exists');
            }

            /** @var Form $form */
            $form = $this['form'];

            $defaults = $row->toArray();

            $form->setDefaults($defaults);
        }

        parent::render();
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();

        $nullable = $form->addCheckbox(SettingsRepository::COLUMN_NULLABLE, $this->translator->translate('admin.settings.field.nullable'))
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlAttribute("style", "width: auto;")
            ->setDefaultValue(false);

        $form->addText(SettingsRepository::COLUMN_NAME, $this->translator->translate('admin.settings.field.name'))
            ->setHtmlAttribute("class", "form-control")
            ->setRequired($this->translator->translate('admin.settings.field.error.name_required'));

        $form->addText(SettingsRepository::COLUMN_CONTENT, $this->translator->translate('admin.settings.field.content'))
            ->setHtmlAttribute("class", "form-control")
            ->addConditionOn($nullable, Form::FILLED)
            ->setRequired($this->translator->translate('admin.settings.field.error.content_required'));

        $form->addSubmit('save', $this->translator->translate('common.field.save'))
            ->setHtmlAttribute("class", "btn btn-info");

        $form->onValidate[] = [$this, 'validateForm'];

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    public function validateForm(Form $form, ArrayHash $values): void
    {
        /** @var bool $nullable */
        $nullable = $values[SettingsRepository::COLUMN_NULLABLE] ?? false;

        if (!$nullable && empty($values[SettingsRepository::COLUMN_CONTENT]))
        {
            $form->addError($this->translator->translate('admin.settings.field.error.content_required'));
        }

        $existing = $this->settingRepository->getByName($values[SettingsRepository::COLUMN_NAME]);

        if ($existing !== null && $this->id !== $existing[SettingsRepository::COLUMN_ID])
        {
            $form->addError($this->translator->translate('admin.settings.field.error.name_used'));
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values): void
    {
        try
        {
            $data = (array)$values;
            if ($this->id !== null)
            {
                $data[SettingsRepository::COLUMN_ID] = $this->id;
            }
            $this->settingRepository->save($data);
            $this->presenter->redirect("Settings:");
        }
        catch (AbortException $exception)
        {
            throw $exception;
        }
        catch (Exception $exception)
        {
            Debugger::log($exception, ILogger::EXCEPTION);
            $form->addError($this->translator->translate('common.form.save_error'));
        }
    }
}

interface ISettingsFormFactory
{
    /**
     * @param int|null $id
     * @return SettingsForm
     */
    public function create(?int $id = null): SettingsForm;
}