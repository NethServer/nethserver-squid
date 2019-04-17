<?php
namespace NethServer\Module\Proxy\BypassDomains;

/*
 * Copyright (C) 2019 Nethesis S.r.l.
 * http://www.nethesis.it - nethserver@nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see COPYING.
 */

use Nethgui\System\PlatformInterface as Validate;

/**
 * Configure transparent proxy bypass
 *
 * @author Giacomo Sanchietti
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    // Declare all parameters
    public function initialize()
    {
        //array($this->getAdapter(), 'Domains', NULL, NULL)
        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('BypassDomains', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD, 'Domains'),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('status', 'enabled');

        parent::initialize();
    }

    public function readBypassDomains($v)
    {
        return implode("\n", explode(",", $v));
    }

    public function writeBypassDomains($p)
    {
        return array(implode(',', array_filter(preg_split("/[;,\s]+/", $p))));
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if (!$this->getRequest()->isMutation()) {
            return;
        }

        $hostnameValidator = $this->createValidator(Validate::HOSTNAME_FQDN);
        $domains = array_filter(preg_split('/[,\s]+/', $this->parameters['BypassDomains']));
        foreach ($domains as $domain){
            if( ! $hostnameValidator->evaluate($domain)) {
                $report->addValidationErrorMessage($this, 'BypassDomains', 'valid_bypass', array($domain));
            }
        }
    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-squid-save &');
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if($this->getIdentifier() != 'delete') {
             $view->setTemplate('NethServer\Template\Proxy\BypassDomains');
        } else {
             $view->setTemplate('Nethgui\Template\Table\Delete');
        }
    }
}
