<?php
namespace NethServer\Module\Proxy\Rules;

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
    private $ipRanges = array();
    private $cidrs = array();
    private $actions = array();
    private $objs = array();

    private function prepareVars()
    {
        if (!$this->hosts) {
            foreach ($this->getPlatform()->getDatabase('hosts')->getAll() as $key => $values) {
                if ($values['type'] == 'local' || $values['type'] == 'host') {
                    $this->hosts[$key] = $values;
                }
            }
        }
        if (!$this->ipRanges) {
            $this->ipRanges = $this->getPlatform()->getDatabase('hosts')->getAll('iprange');
        }
        if (!$this->cidrs) {
            $this->cidrs = $this->getPlatform()->getDatabase('hosts')->getAll('cidr');
        }
        if (!$this->actions) {
            foreach ($this->getPlatform()->getDatabase('networks')->getAll('provider') as $key => $props) {
                $i = $this->getPlatform()->getDatabase('networks')->getKey($props['interface']);
                if (isset($i['ipaddr']) && $i['ipaddr']) { # force traffic only for red with static IP
                    $this->actions[] = 'force;'.$key;
                }
                $this->actions[] = 'provider;'.$key;
            }
            $this->actions[] = 'priority;low';
            $this->actions[] = 'priority;high';
        }
        if (!$this->objs) {
            foreach ($this->arrayToDatasource($this->hosts,'host') as $pair) {
                $this->objs[] = $pair[0];
            }
            foreach ($this->arrayToDatasource($this->ipRanges,'iprange') as $pair) {
                $this->objs[] = $pair[0];
            }
            foreach ($this->arrayToDatasource($this->cidrs,'cidr') as $pair) {
                $this->objs[] = $pair[0];
            }
        }
    }

    // Declare all parameters
    public function initialize()
    {
        $this->prepareVars();

        $parameterSchema = array(
            array('id', $this->createValidator()->integer(), \Nethgui\Controller\Table\Modify::KEY),
            array('Src', $this->createValidator()->memberOf($this->objs), \Nethgui\Controller\Table\Modify::FIELD),
            array('Action', $this->createValidator()->memberOf($this->actions), \Nethgui\Controller\Table\Modify::FIELD),
            array('Dst', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('status', 'enabled');

        parent::initialize();
    }

    public function readDst($v)
    {
        return implode("\n", explode(",", $v));
    }

    public function writeDst($p)
    {
        return array(implode(',', array_filter(preg_split("/[,\s]+/", $p))));
    }

    private function nextKey()
    {
        $db = $this->getPlatform()->getDatabase('squid');
        $max = max(array_keys($db->getAll('rule')));
        return $max+1;
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $v_domain = $this->createValidator()->hostname(1);
        if($this->parameters['Dst'] && $this->getRequest()->isMutation()) {
            $domains = array_filter(preg_split('/[,\s]+/', $this->parameters['Dst']));
            foreach ($domains as $d){
                if( ! $v_domain->evaluate($d)) {
                    $report->addValidationError($this, $d, $v_domain);
                }
            }
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
             $view->setTemplate('NethServer\Template\Proxy\RulesModify');
        } else {
             $view->setTemplate('Nethgui\Template\Table\Delete');
        }
        $this->prepareVars();

        if ($this->getIdentifier() == 'create') {
            $view['id'] =  $this->nextKey();
        }
        $h = $view->translate('Hosts_label');
        $ir = $view->translate('IpRanges_label');
        $c = $view->translate('CIDRs_label');
        $hosts = $this->arrayToDatasource($this->hosts,'host');
        $ranges = $this->arrayToDatasource($this->ipRanges,'iprange');
        $cidrs = $this->arrayToDatasource($this->cidrs,'cidr');
        $view['SrcDatasource'] = array(array($hosts,$h),array($ranges,$ir),array($cidrs,$c));
        $view['ActionDatasource'] = array_map(function($fmt) use ($view) {
            $tmp = explode(";",$fmt);
            if ($tmp[0] == 'priority') {
                return array($fmt, $view->translate($tmp[1] . '_label'). " " . $view->translate($tmp[0] . '_label'));
            } else {
                return array($fmt, $view->translate($tmp[0] . '_label'). " " . $view->translate($tmp[1]));
            }
        }, $this->actions);

    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-squid-save &');
    }
}
