<?php
/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->textInput('Ip');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);