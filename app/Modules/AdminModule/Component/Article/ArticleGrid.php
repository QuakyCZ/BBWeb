<?php

namespace App\Modules\AdminModule\Component\Article;

use App\Component\BaseDataGrid;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\ArticleRepository;
use App\Repository\Primary\UserRepository;
use Nette\Application\BadRequestException;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Tracy\Debugger;
use Tracy\ILogger;
use Ublaboo\DataGrid\Exception\DataGridException;

class ArticleGrid extends BaseDataGrid
{

    private ArticleRepository $articleRepository;

    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        ArticleRepository $articleRepository,
        private UserRepository $userRepository
    )
    {
        parent::__construct($parent, 'articleGrid', $translator);

        $this->articleRepository = $articleRepository;
    }

    /**
     * @return Selection
     */
    protected function getSelection(): Selection
    {
        return $this->articleRepository->findAll();
    }

    /**
     * @return void
     * @throws DataGridException
     */
    protected function createGrid(): void
    {
        $this->grid->setDefaultSort(ArticleRepository::COLUMN_CREATED . ' DESC');
        $this->addColumns();
        $this->addActions();
        $this->addToolbarActions();
    }

    private function addColumns(): void
    {
        $this->grid->addColumnNumber(ArticleRepository::COLUMN_ID, $this->translator->translate('admin.articles.field.id'))
            ->setSortable();

        $this->grid->addColumnText(ArticleRepository::COLUMN_TITLE, $this->translator->translate('admin.articles.field.title'))
            ->setFilterText();

        $this->grid->addColumnText(ArticleRepository::COLUMN_CREATED_USER_ID, $this->translator->translate('admin.articles.field.author'))
            ->setRenderer(function (ActiveRow $article)
            {
                $userRow = $article->ref(UserRepository::TABLE_NAME);
                if ($userRow === null)
                {
                    return '';
                }

                return $userRow[UserRepository::COLUMN_USERNAME];
            })
            ->setFilterSelect($this->userRepository->fetchForChoiceControl())->setPrompt('Nezáleží');

        $this->grid->addColumnDateTime(ArticleRepository::COLUMN_CREATED, $this->translator->translate('admin.articles.field.created'))
            ->setFormat('d. m. Y H:i')
            ->setSortable();

        $this->grid->addColumnText(ArticleRepository::COLUMN_IS_PUBLISHED, $this->translator->translate('admin.articles.field.published'))
            ->setTemplateEscaping(false)
            ->setReplacement([
                0 => '<i class="fa-regular fa-square text-danger"></i>',
                1 => '<i class="fa-regular fa-square-check text-success"></i>'
            ])->setFilterSelect([
                null => 'Nezáleží',
                1 => 'Ano',
                0 => 'Ne',
            ]);

        $this->grid->addColumnText(ArticleRepository::COLUMN_IS_PINNED, $this->translator->translate('admin.articles.field.pinned'))
            ->setTemplateEscaping(false)
            ->setReplacement([
                0 => '<i class="fa-regular fa-square text-danger"></i>',
                1 => '<i class="fa-regular fa-square-check text-success"></i>'
            ])->setFilterSelect([
                null => 'Nezáleží',
                1 => 'Ano',
                0 => 'Ne',
            ]);
    }


    /**
     * @return void
     * @throws DataGridException
     */
    private function addActions(): void
    {
        // PUBLISH
        $this->grid->addActionCallback('publish', '',function ($id)
        {
            try
            {
                $row = $this->articleRepository->findRow($id);
                if ($row === null)
                {
                    throw new BadRequestException();
                }
                $row->update([
                    ArticleRepository::COLUMN_IS_PUBLISHED => 1
                ]);
                $this->grid->presenter->flashMessage('Článek byl publikován.', EFlashMessageType::SUCCESS);
            }
            catch (\PDOException $exception)
            {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }
        })
            ->setRenderCondition(function (ActiveRow $row)
        {
            return !$row[ArticleRepository::COLUMN_IS_PUBLISHED];
        })
            ->setIcon('eye')
            ->setTitle($this->translator->translate('admin.articles.publish'))
            ->setClass('btn btn-info');


        // UNPUBLISH
        $this->grid->addActionCallback('unpublish', '', function ($id)
        {
            try
            {
                $row = $this->articleRepository->findRow($id);
                if ($row === null)
                {
                    throw new BadRequestException();
                }
                $row->update([
                    ArticleRepository::COLUMN_IS_PUBLISHED => 0
                ]);
                $this->grid->presenter->flashMessage('Publikování článeku bylo zrušeno.', EFlashMessageType::SUCCESS);
            }
            catch (\PDOException $exception)
            {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }
        })
            ->setIcon('eye-slash')
            ->setTitle($this->translator->translate('admin.articles.unpublish'))
            ->setRenderCondition(function (ActiveRow $row)
            {
                return ((bool) $row[ArticleRepository::COLUMN_IS_PUBLISHED]);
            })
            ->setClass('btn btn-danger');

        // PIN
        $this->grid->addActionCallback('pin', '', function ($id)
        {
            try
            {
                $row = $this->articleRepository->findRow($id);
                if ($row === null)
                {
                    return;
                }

                $row->update([
                    ArticleRepository::COLUMN_IS_PINNED => 1
                ]);

                $this->grid->presenter->flashMessage('Příspěvek byl připnut.', EFlashMessageType::SUCCESS);
            }
            catch (\PDOException $exception)
            {
                Debugger::log($exception);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }

        })
            ->setRenderCondition(function (ActiveRow $row)
            {
                return !$row[ArticleRepository::COLUMN_IS_PINNED];
            })
            ->setTitle($this->translator->translate('admin.articles.pin'))
            ->setIcon('thumbtack')
            ->setClass('btn btn-info');

        // UNPIN
        $this->grid->addActionCallback('unpin', '', function ($id)
        {
            try
            {
                $row = $this->articleRepository->findRow($id);
                if ($row === null)
                {
                    return;
                }

                $row->update([
                    ArticleRepository::COLUMN_IS_PINNED => 0
                ]);

                $this->grid->presenter->flashMessage('Příspěvek byl odepnut.', EFlashMessageType::SUCCESS);
            }
            catch (\PDOException $exception)
            {
                Debugger::log($exception);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }

        })
            ->setRenderCondition(function (ActiveRow $row)
            {
                return (bool)$row[ArticleRepository::COLUMN_IS_PINNED] === true;
            })
            ->setTitle($this->translator->translate('admin.articles.pin'))
            ->setIcon('thumbtack')
            ->setClass('btn btn-danger');

        // EDIT
        $this->grid->addAction('edit', '', ':Admin:Article:edit', [
            'id' => 'id'
        ])
            ->setTitle($this->translator->translate('admin.articles.edit'))
            ->setIcon('pen')
            ->setClass('btn btn-warning');

        // DELETE
        $this->grid->addActionCallback('delete', '', function ($id)
        {
            try
            {
                $this->articleRepository->setNotDeletedNull($id);
                $this->grid->presenter->flashMessage('Příspěvek byl smazán.', EFlashMessageType::SUCCESS);
            }
            catch (\PDOException $exception)
            {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }
        })
            ->setTitle($this->translator->translate('admin.articles.delete'))
            ->setIcon('trash')
            ->setClass('btn btn-danger');
    }


    /**
     * @return void
     * @throws DataGridException
     */
    public function addToolbarActions(): void
    {
        $this->grid->addToolbarButton(':Admin:Article:add', 'Přidat článek')
            ->setIcon('plus')
            ->setClass('btn btn-success');
    }
}