<?php

namespace App\Model;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;

class LinkGenerator implements ILinkGenerator
{
    public function __construct(
        private \Nette\Application\LinkGenerator $linkGenerator
    ) {
    }

    public function link(IMenuItem $item): string
    {
        $action = $item->getAction();

        if ($action !== null) {
            return $this->linkGenerator->link($action, $item->getActionParameters());
        }

        return $item->getLink() ?? '#';
    }
}
