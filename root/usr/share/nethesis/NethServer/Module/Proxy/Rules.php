<?php
namespace NethServer\Module\Proxy;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
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
 * Bypass transparent proxy
 */
class Rules extends \Nethgui\Controller\TableController
{
    public $sortId = 40;

    public function initialize()
    {
        $columns = array(
            'Key',
            'Src',
            'Action',
            'Dst',
            'Description',
            'Actions',
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('squid', 'rule'))
            ->setColumns($columns)            
            ->addRowAction(new \NethServer\Module\Proxy\Rules\Modify('update')) 
            ->addRowAction(new \NethServer\Module\Proxy\Rules\Modify('delete'))
            ->addTableAction(new \NethServer\Module\Proxy\Rules\Modify('create')) 
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
        ;

        parent::initialize();
    }

    private function formatObject(\Nethgui\View\ViewInterface $view, $val, $default='any_label') {
        if (!$val) {
            return $view->translate($default);
        }
        $tmp = explode(';',$val);
        return $view->translate($tmp[0].'_label').": ".$tmp[1];
    }

    public function prepareViewForColumnKey(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if (!isset($values['status']) || ($values['status'] == 'disabled')) {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' user-locked');
        }
        return strval($key);
    }

    public function prepareViewForColumnSrc(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        return $this->formatObject($view, $values['Src']);
    }

    public function prepareViewForColumnAction(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $tmp = explode(";",$values['Action']);
        if ($tmp[0] == 'priority') {
            return $view->translate($tmp[1] . '_label'). " " . $view->translate($tmp[0] . '_label');
        } else {
            return $view->translate($tmp[0] . '_label'). " " . $view->translate($tmp[1]);
        }
    }


}
