<?php
namespace NethServer\Module\Proxy;

/*
 * Copyright (C) 2017 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;

/**
 * Configure squid byapss domains
 *
 */
class BypassDomains extends \Nethgui\Controller\AbstractController
{

    public $sortId = 20;


    // Declare all parameters
    public function initialize()
    {
        parent::initialize();

        $this->declareParameter('BypassDomains', Validate::ANYTHING, array('configuration', 'squid', 'BypassDomains'));
    }

    public function readBypassDomains($v)
    {
        return implode("\n", explode(",", $v));
    }

    public function writeBypassDomains($p)
    {
        return array(implode(',', array_filter(preg_split("/[,\s]+/", $p))));
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
}
