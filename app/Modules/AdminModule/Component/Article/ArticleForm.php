<?php

namespace App\Modules\AdminModule\Component\Article;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\ArticleRepository;
use DateTime;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PDOException;
use Tracy\Debugger;
use Tracy\ILogger;

class ArticleForm extends BaseComponent
{
    /**
     * @param int|null $id
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        private ?int $id,
        private ArticleRepository $articleRepository
    ) {
    }

    /**
     * @throws BadRequestException
     */
    public function render(): void
    {
        if ($this->id !== null) {
            $article = $this->articleRepository->findRow($this->id);

            if ($article === null) {
                throw new BadRequestException();
            }

            $defaults = $article->toArray();

            /** @var Form $form */
            $form = $this['form'];

            $form->setDefaults($defaults);
        }

        parent::render();
    }

    public function createComponentForm(): Form
    {
        $form = new Form();

        $form->addText(ArticleRepository::COLUMN_TITLE, 'Název');
        $form->addTextArea(ArticleRepository::COLUMN_TEXT, 'Text');

        if ($this->presenter->user->isInRole('ADMIN')) {
            $form->addCheckbox(ArticleRepository::COLUMN_IS_PUBLISHED, 'Publikovat');
            $form->addCheckbox(ArticleRepository::COLUMN_IS_PINNED, 'Připnout');
        }

        $form->addSubmit('save', 'Uložit');
        
        $form->addButton('close', 'Zavřít')
            ->setHtmlAttribute('onclick', 'window.location="' . $this->presenter->link('Article:').'"');

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values): void
    {
        $array = (array) $values;
        if (array_key_exists('close', $array)) {
            unset($array['close']);
        }
        if (empty($array[ArticleRepository::COLUMN_IS_PUBLISHED])) {
            $array[ArticleRepository::COLUMN_IS_PUBLISHED] = false;
        }

        if (empty($array[ArticleRepository::COLUMN_IS_PINNED])) {
            $array[ArticleRepository::COLUMN_IS_PINNED] = false;
        }

        if ($this->id === null) {
            $array[ArticleRepository::COLUMN_CREATED_USER_ID] = $this->presenter->user->id;
        } else {
            $array[ArticleRepository::COLUMN_CHANGED_USER_ID] = $this->presenter->user->id;
            $array[ArticleRepository::COLUMN_CHANGED] = new DateTime();
        }

        try {
            if ($this->id) {
                $this->articleRepository->findRow($this->id)->update($array);
            } else {
                $this->articleRepository->save($array);
            }
            $this->presenter->flashMessage('Článek byl uložen.', EFlashMessageType::SUCCESS);

            $this->presenter->redirect('this');
        } catch (PDOException $exception) {
            Debugger::log($exception, ILogger::EXCEPTION);
            $form->addError('Při ukládání nastala chyba.');
        }
    }
}
