<?php

namespace App\Form;

use App\Form\Markdown\MarkdownTrait;
use App\Form\MultiSelectBox2\MultiSelectBox2Trait;

class Form extends \Nette\Application\UI\Form
{
    use MarkdownTrait;
    use MultiSelectBox2Trait;
}