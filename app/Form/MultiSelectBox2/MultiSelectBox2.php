<?php

namespace App\Form\MultiSelectBox2;

use Nette\Forms\Controls\MultiSelectBox;
use Nette\Utils\Html;

class MultiSelectBox2 extends MultiSelectBox
{
    /**
     * @return Html
     */
    public function getControl(): Html
    {
         return parent::getControl()
            ->class('form-select form-select-sm multiselect2')
            ->appendAttribute('multiple', '');
    }
}