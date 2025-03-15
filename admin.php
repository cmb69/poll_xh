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

use Poll\Dic;

if (!defined("CMSIMPLE_XH_VERSION")) {
    http_response_code(403);
    exit;
}

/**
 * @var string $admin
 * @var string $o
 */

XH_registerStandardPluginMenuItems(true);
if (XH_wantsPluginAdministration("poll")) {
    $o .= print_plugin_admin("on");
    switch ($admin) {
        case "":
            $o .= Dic::infoController()->defaultAction();
            break;
        case "plugin_main":
            $o .= Dic::mainAdminController()->defaultAction();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
