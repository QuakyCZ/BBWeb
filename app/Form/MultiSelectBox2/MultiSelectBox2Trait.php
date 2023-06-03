<?php

namespace App\Form\MultiSelectBox2;

trait MultiSelectBox2Trait
{
    /**
     * Adds multiselectbox to the form.
     * @param string $name
     * @param string|null $label
     * @param array $items
     * @return MultiSelectBox2
     */
    public function addMultiSelect2(string $name, string $label = null, array $items = []): MultiSelectBox2
    {
        $input = new MultiSelectBox2($label, $items);
        $this->addComponent($input, $name);
        return $input;
    }
}