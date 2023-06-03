<?php

namespace App\Form\Markdown;

use App\Form\Markdown\Markdown;

trait MarkdownTrait
{
    /**
     * Adds markdown textarea to the form.
     * @param string $name
     * @param string|null $label
     * @return Markdown
     */
    public function addMarkdown(string $name, string $label = null): Markdown
    {
        $input = new Markdown($label);
        $this->addComponent($input, $name);
        return $input;
    }
}