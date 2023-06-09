<?php

namespace App\Form\Markdown;

use Nette;
use Nette\Forms\Controls\TextArea;

class Markdown extends TextArea
{
    public function getControl(): Nette\Utils\Html
    {
        return parent::getControl()
            ->class('markdown-editor');
    }
}