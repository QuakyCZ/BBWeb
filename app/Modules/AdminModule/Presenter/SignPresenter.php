<?php

namespace App\Modules\AdminModule\Presenter;


use App\Modules\AdminModule\Component\SignInForm\ISignInFormFactory;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

class SignPresenter extends Presenter
{

    protected function startup() {
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
    }

    private ISignInFormFactory $signInFormFactory;

    public function __construct(ISignInFormFactory $signInFormFactory)
    {
        parent::__construct();
        $this->signInFormFactory = $signInFormFactory;
    }

    /**
     * @throws AbortException
     */
    public function actionIn() {
        if($this->user->isLoggedIn())
            $this->redirect('Default:');
    }

    /**
     * @throws AbortException
     */
    public function actionOut() {
        if(!$this->user->isLoggedIn())
            $this->redirect('Default:');

        $this->user->logout(true);
        $this->redirect('Default:');
    }


    public function createComponentSignInForm() {
        return $this->signInFormFactory->create();
    }
}