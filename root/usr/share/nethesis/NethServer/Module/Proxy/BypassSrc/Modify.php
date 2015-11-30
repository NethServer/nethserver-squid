<?php
namespace NethServer\Module\Proxy\BypassSrc;

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
 * Configure transparent proxy bypass
 *
 * @author Giacomo Sanchietti
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $hosts = array();
    private $hostGroups = array();
    private $ipRanges = array();
    private $cidrs = array();

    private function prepareVars()
    {
        if (!$this->hosts) {
            foreach ($this->getPlatform()->getDatabase('hosts')->getAll() as $key => $values) {
                if ($values['type'] == 'local' || $values['type'] == 'remote' || $values['type'] == 'host') {
                    $this->hosts[$key] = $values;
                }
            }
        }
        if (!$this->hostGroups) {
            $this->hostGroups = $this->getPlatform()->getDatabase('hosts')->getAll('host-group');
        }
        if (!$this->ipRanges) {
            $this->ipRanges = $this->getPlatform()->getDatabase('hosts')->getAll('iprange');
        }
        if (!$this->cidrs) {
            $this->cidrs = $this->getPlatform()->getDatabase('hosts')->getAll('cidr');
        }
    }

    // Declare all parameters
    public function initialize()
    {
        $this->prepareVars();
 
        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Host', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('status', 'enabled');

        parent::initialize();
    }

        private function keyExists($key)
    {
        $tmp = explode(';', $key);
        if ($tmp[0] == 'host' || $tmp[0] == 'host-group' || $tmp[0] == 'iprange' || $tmp[0] == 'cidr') {
            $db = 'hosts';
        } else {
            $db = 'proxy';
        }

        return ($this->getPlatform()->getDatabase($db)->getType($tmp[1]) != '');
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $keyExists = $this->keyExists('bypass;'.$this->parameters['name']);
        if ($this->getIdentifier() === 'create' && $keyExists) {
            $report->addValidationErrorMessage($this, 'name', 'key_exists_message');
        }
        if ($this->getIdentifier() && $this->parameters['Host'] && !$this->keyExists($this->parameters['Host'])) {
            $report->addValidationErrorMessage($this, 'Host', 'key_doesnt_exists_message');
        }
        parent::validate($report);
    }

    private function arrayToDatasource($array, $prefix)
    {
        $ret = array();
        foreach($array as $key => $props) {
            $ret[] = array($prefix.';'.$key, $key);
        }
        return $ret;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
	if($this->getIdentifier() != 'delete') {
             $view->setTemplate('NethServer\Template\Proxy\BypassModify');
        } else {
             $view->setTemplate('Nethgui\Template\Table\Delete');
        }
        $this->prepareVars();

        $h = $view->translate('Hosts_label');
        $hg = $view->translate('HostGroups_label');
        $ir = $view->translate('IpRanges_label');
        $c = $view->translate('CIDRs_label');
        $hosts = $this->arrayToDatasource($this->hosts,'host');
        $groups = $this->arrayToDatasource($this->hostGroups,'host-group');
        $ranges = $this->arrayToDatasource($this->ipRanges,'iprange');
        $cidrs = $this->arrayToDatasource($this->cidrs,'cidr');
        $view['HostDatasource'] = array(array($hosts,$h),array($groups,$hg),array($ranges,$ir),array($cidrs,$c));
    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-squid-save &');
    }
}
