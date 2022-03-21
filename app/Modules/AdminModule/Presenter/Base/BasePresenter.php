<?php

namespace App\Modules\AdminModule\Presenter\Base;


use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
    protected function startup()
    {

        $latte = $this->template->getLatte();

        $set = new MacroSet($latte->getCompiler());

        $set->addMacro(
            'addCss',
            function (MacroNode $node, PhpWriter $writer) {
                $txt = '<link rel="stylesheet" type="text/css" href=%node.word>';
                return $writer->write("echo '".$txt."'");
            }
        );

        parent::startup();

        if(!$this->user->isLoggedIn())
            $this->redirect('Sign:in');
    }
}