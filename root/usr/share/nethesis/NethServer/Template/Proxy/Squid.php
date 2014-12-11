<?php

$green_modes = $view->fieldset('GreenMode')->setAttribute('template', $T('GreenMode_label'))
            ->insert($view->radioButton('GreenMode', 'manual'))
            ->insert($view->radioButton('GreenMode', 'authenticated'))
            ->insert($view->radioButton('GreenMode', 'transparent'))
            ->insert($view->radioButton('GreenMode', 'transparent_ssl'));

$blue_modes = $view->fieldset('BlueMode')->setAttribute('template', $T('BlueMode_label'))
            ->insert($view->radioButton('BlueMode', 'manual'))
            ->insert($view->radioButton('BlueMode', 'authenticated'))
            ->insert($view->radioButton('BlueMode', 'transparent'))
            ->insert($view->radioButton('BlueMode', 'transparent_ssl'));


echo $view->fieldsetSwitch('status', 'enabled',  $view::FIELDSETSWITCH_CHECKBOX)
        ->setAttribute('template', $T('Squid_status'))
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($green_modes)
        ->insert($blue_modes)
        ->insert($view->checkbox('PortBlock', 'enabled')->setAttribute('uncheckedValue', 'disabled'));

echo "<ul style='margin-bottom: 10px; margin-left: 5px; padding: 3px'><li>".$view->translate('ca_download')."</li><li><ul>";
foreach ($view['ips'] as $ip) {
    echo "<li><a href='http://$ip/proxy.crt'>http://$ip/proxy.crt</a></li>";
}
echo "</ul></li></ul>";

echo $view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('ProxyAdvanced_label'))
    ->insert($view->textInput('ParentProxy')->setAttribute('placeholder','192.168.0.1:8080'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
