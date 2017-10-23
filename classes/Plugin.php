<?php

/**
 * Copyright 2012-2017 Christoph M. Becker
 *
 * This file is part of Poll_XH.
 *
 * Poll_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Poll_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Poll_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Poll;

use stdClass;

class Plugin
{
    /**
     * @return void
     */
    public static function dispatch()
    {
        if (XH_ADM && XH_wantsPluginAdministration('poll')) {
            self::handleAdministration();
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    protected static function isVoting($name)
    {
        return isset($_POST['poll_' . $name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function main($name)
    {
        global $e, $plugin_tx;

        if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
            $e = '<li><b>'
                . sprintf($plugin_tx['poll']['error_invalid_name'], $name)
                . '</b></li>' . PHP_EOL;
            return false;
        }
        $o = '';
        if (self::isVoting($name)) {
            ob_start();
            (new WidgetController($name))->voteAction();
            $o .= ob_get_clean();
        } else {
            ob_start();
            (new WidgetController($name))->defaultAction();
            $o .= ob_get_clean();
        }
        return $o;
    }

    /**
     * @return void
     */
    protected static function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController)->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                ob_start();
                (new MainAdminController)->defaultAction();
                $o .= ob_get_clean();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'poll');
        }
    }
}
