<?php
namespace NethServer\Module\Proxy;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
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
 * Configure squid behaviour
 *
 * @author Giacomo Sanchietti
 */
class Squid extends \Nethgui\Controller\AbstractController
{

    public $sortId = 10;
    
    public $modes = array('manual','authenticated','transparent','transparent_ssl');
 
    // Declare all parameters
    public function initialize()
    {
        parent::initialize();

        $this->declareParameter('status', Validate::SERVICESTATUS, array('configuration', 'squid', 'status'));
        $this->declareParameter('GreenMode', $this->createValidator()->memberOf($this->modes), array('configuration', 'squid', 'GreenMode'));
        $this->declareParameter('BlueMode', $this->createValidator()->memberOf($this->modes), array('configuration', 'squid', 'BlueMode'));
        $this->declareParameter('ParentProxy', Validate::ANYTHING, array('configuration', 'squid', 'ParentProxy'));
        $this->declareParameter('PortBlock', Validate::SERVICESTATUS, array('configuration', 'squid', 'PortBlock'));
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if ($this->getRequest()->isMutation()) {
            if ($this->parameters['ParentProxy']) {
                $parts = explode(':', $this->parameters['ParentProxy']);
                $vh = $this->createValidator(Validate::HOSTADDRESS);
                if ( ! $vh->evaluate($parts[0]) ) {
                    $report->addValidationError($this, 'ParentProxy', $vh);
                }
                if (isset($parts[1])) {
                    $vp = $this->createValidator(Validate::PORTNUMBER);
                    if ( ! $vp->evaluate($parts[1]) ) {
                        $report->addValidationError($this, 'ParentProxy', $vp);
                    }
                }
            }
        }

        parent::validate($report);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $ips = array();
        foreach ($this->getPlatform()->getDatabase('networks')->getAll() as $key => $values) {
            if (isset($values['role']) && $values['role'] == 'green') {
                $ips[] = $values['ipaddr'];
            }
        }
        $view['ips'] = $ips;
    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-squid-save');
    }
}
