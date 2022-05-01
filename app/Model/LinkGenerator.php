<?php

namespace App\Model;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;

class LinkGenerator implements ILinkGenerator
{

    /**
     * @var \Nette\Application\LinkGenerator
     */
    private $linkGenerator;

    public function __construct
    (
        $baseUrl,
        \Nette\Application\LinkGenerator $linkGenerator
    )
    {
        $this->linkGenerator = $linkGenerator->withReferenceUrl($baseUrl);
    }

    public function link(IMenuItem $item): string
    {
        $action = $item->getAction();
        if ($action !== null) {
            return $this->linkGenerator->link($action, $item->getActionParameters());
        }

        $link = $item->getLink();
        if ($link !== null) {
            return $link;
        }

        return '#';
    }
}