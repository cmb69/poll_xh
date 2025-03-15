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
    const VERSION = '1.0beta3';

    /**
     * @return void
     */
    public function run()
    {
        if (XH_ADM) { // @phpstan-ignore-line
            XH_registerStandardPluginMenuItems(true);
            if (XH_wantsPluginAdministration('poll')) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * @return void
     */
    private function handleAdministration()
    {
        global $admin, $o;

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
                $o .= plugin_admin_common();
        }
    }
}
