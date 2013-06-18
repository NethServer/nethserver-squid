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
        $this->declareParameter('Mode', $this->createValidator()->memberOf($this->modes), array('configuration', 'squid', 'Mode'));
        $this->declareParameter('ParentProxy', Validate::ANYTHING, array('configuration', 'squid', 'ParentProxy'));
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['ModeDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $view->translate($fmt . '_label'));
        }, $this->modes);

    }
    
    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-squid-save@post-process');
    }
}
