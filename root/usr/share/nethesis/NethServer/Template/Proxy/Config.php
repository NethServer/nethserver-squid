<?php

echo $view->panel()
    ->insert($view->radioButton('DiskCache', 'disabled')->setAttribute('label', $T('DiskCache_disabled_label')))
    ->insert($view->fieldsetSwitch('DiskCache', 'enabled', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('Webvirt_label'))
        ->insert($view->textInput('DiskCacheSize')->setAttribute('label', $T('DiskCacheSize_label'))
        )
        ->insert($view->textInput('MinObjSize')->setAttribute('label', $T('MinObjSize_label'))
        )
        ->insert($view->textInput('MaxObjSize')->setAttribute('label', $T('MaxObjSize_label'))
        )
    );

echo '<br>';
echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP)
     ->insert($view->button('Clear_cache', $view::BUTTON_SUBMIT)->setAttribute('label', $T('ClearCache_label')));
