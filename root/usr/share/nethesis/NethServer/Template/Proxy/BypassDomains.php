<?php

echo $view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0));
echo $view->checkbox('status', 'enabled')
        ->setAttribute('template', $T('status'))
        ->setAttribute('uncheckedValue', 'disabled');
echo $view->textInput('Description');

echo $view->panel()
        ->insert($view->textArea('BypassDomains', $view::LABEL_ABOVE)->setAttribute('dimensions', '10x50'))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
