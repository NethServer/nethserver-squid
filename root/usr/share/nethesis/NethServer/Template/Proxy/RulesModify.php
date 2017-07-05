<?php

echo $view->textInput('id', $view::STATE_READONLY);
echo $view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX)
        ->setAttribute('template', $T('status'))
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->selector('Src', $view::SELECTOR_DROPDOWN))
        ->insert($view->selector('Action', $view::SELECTOR_DROPDOWN))
        ->insert($view->textArea('Dst', $view::LABEL_ABOVE)->setAttribute('dimensions', '10x50'))
        ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
