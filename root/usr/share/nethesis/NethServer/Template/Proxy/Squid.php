<?php

echo $view->fieldsetSwitch('status', 'enabled',  $view::FIELDSETSWITCH_CHECKBOX)
        ->setAttribute('template', $T('Squid_status'))
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->selector('Mode','transparent'));

echo $view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('ProxyAdvanced_label'))
    ->insert($view->textInput('ParentProxy')->setAttribute('placeholder','192.168.0.1:8080'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
