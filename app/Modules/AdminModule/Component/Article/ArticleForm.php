<?php

namespace App\Modules\AdminModule\Component\Article;

use App\Component\BaseComponent;
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

        parent::render(); // TODO: Change the autogenerated stub
    }

    public function createComponentForm(): Form
    {
        $form = new Form();

        $form->addText(ArticleRepository::COLUMN_TITLE, 'Název');
        $form->addTextArea(ArticleRepository::COLUMN_TEXT, 'Text', null, 8);

        if ($this->presenter->user->isInRole('ADMIN')) {
            $form->addCheckbox(ArticleRepository::COLUMN_IS_PUBLISHED, 'Publikovat');
            $form->addCheckbox(ArticleRepository::COLUMN_IS_PINNED, 'Připnout');
        }

        $form->addSubmit('save', 'Uložit');

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
        if (empty($values[ArticleRepository::COLUMN_IS_PUBLISHED])) {
            $values[ArticleRepository::COLUMN_IS_PUBLISHED] = false;
        }

        if (empty($values[ArticleRepository::COLUMN_IS_PINNED])) {
            $values[ArticleRepository::COLUMN_IS_PINNED] = false;
        }

        if ($this->id === null) {
            $values[ArticleRepository::COLUMN_CREATED_USER_ID] = $this->presenter->user->id;
        } else {
            $values[ArticleRepository::COLUMN_CHANGED_USER_ID] = $this->presenter->user->id;
            $values[ArticleRepository::COLUMN_CHANGED] = new DateTime();
        }

        try {
            if ($this->id) {
                $this->articleRepository->findRow($this->id)->update($values);
            } else {
                $this->articleRepository->save((array) $values);
            }

            $this->presenter->redirect('Article:');
        } catch (PDOException $exception) {
            Debugger::log($exception, ILogger::EXCEPTION);
            $form->addError('Při ukládání nastala chyba.');
        }
    }
}
