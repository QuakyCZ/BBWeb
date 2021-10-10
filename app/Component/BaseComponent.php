<?php

namespace App\Component;

use Nette\Application\UI\Control;

class BaseComponent extends Control
{
    private ?string $componentName = null;
    private ?string $templatePath = null;
    private ?string $componentPath = null;

    public function getComponentName(): string {
        if($this->componentName)
            $this->componentName = $this->getReflection()->getShortName();

        return $this->componentName;
    }

    public function getPathToComponent(): string {
        if($this->componentPath === NULL) {
            $file = $this->getReflection()->getFileName();
            if(!empty($file)) {
                $this->componentPath = str_replace('.php','',$file);
            }
        }
        return $this->componentPath;
    }

    public function getLatteFile(): string {
        if($this->templatePath === NULL)
            $this->templatePath = $this->getPathToComponent().'.latte';

        return $this->templatePath;
    }

    public function render(): void {
        $this->getTemplate()->setFile($this->getLatteFile());
        $this->getTemplate()->render();
    }
}