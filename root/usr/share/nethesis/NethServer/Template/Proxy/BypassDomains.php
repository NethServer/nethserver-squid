<?php

echo $view->panel()
        ->insert($view->textArea('BypassDomains', $view::LABEL_ABOVE)->setAttribute('dimensions', '10x50'))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
