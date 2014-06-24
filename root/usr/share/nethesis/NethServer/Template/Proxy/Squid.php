<?php

$modes = $view->fieldset('Mode')->setAttribute('template', $T('Mode_label'))
            ->insert($view->radioButton('Mode', 'manual'))
            ->insert($view->radioButton('Mode', 'authenticated'))
            ->insert($view->radioButton('Mode', 'transparent'))
            ->insert($view->radioButton('Mode', 'transparent_ssl'));

echo $view->fieldsetSwitch('status', 'enabled',  $view::FIELDSETSWITCH_CHECKBOX)
        ->setAttribute('template', $T('Squid_status'))
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($modes)
        ->insert($view->checkbox('PortBlock', 'enabled')->setAttribute('uncheckedValue', 'disabled'));

echo $view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('ProxyAdvanced_label'))
    ->insert($view->textInput('ParentProxy')->setAttribute('placeholder','192.168.0.1:8080'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
