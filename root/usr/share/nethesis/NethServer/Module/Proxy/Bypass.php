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

/**
 * Handle bypass proxy rules by IP address
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Bypass extends \Nethgui\Controller\TableController
{
    public $sortId = 20;

    public function initialize()
    {
        $columns = array(
            'Key',
            'Actions'
        );

        $a = $this->getPlatform()->getTableAdapter('configuration', 'squid', 'Bypass', array(
            ',', '/'));

        $schema = array(
            array('Ip', \Nethgui\System\PlatformInterface::IPv4, \Nethgui\Controller\Table\RowAbstractAction::KEY)
        );

        $createAction = new \Nethgui\Controller\Table\Modify('create');
        $createAction
            ->setViewTemplate('NethServer\Template\Proxy\BypassModify')
            ->setSchema($schema)
        ;

        $deleteAction = new \Nethgui\Controller\Table\Modify('delete');
        $deleteAction
            ->setViewTemplate('Nethgui\Template\Table\Delete')
            ->setSchema($schema)
        ;

        $this
            ->setTableAdapter($a)
            ->setColumns($columns)
            ->addTableAction($createAction)
            ->addRowAction($deleteAction)
        ;

        parent::initialize();
    }

    public function onParametersSaved(\Nethgui\Module\ModuleInterface $currentAction, $changes, $parameters)
    {
        $this->getPlatform()->signalEvent("nethserver-squid-save@post-process");
    }

}
