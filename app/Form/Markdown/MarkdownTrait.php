<?php

namespace App\Form\Markdown;

use App\Form\Markdown\Markdown;

trait MarkdownTrait
{
    /**
     * Adds markdown textarea to the form.
     * @param string $name
     * @param string|null $label
     * @param int|null $entityId
     * @return Markdown
     */
    public function addMarkdown(string $name, string $label = null, ?int $entityId = null): Markdown
    {
        $input = new Markdown($label);
        $input->setHtmlAttribute('data-entityId', $entityId);
        $this->addComponent($input, $name);
        return $input;
    }
}