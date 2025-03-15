<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function widgetController(): WidgetController
    {
        return new WidgetController(self::dataService(), self::view());
    }

    public static function infoController(): InfoController
    {
        global $pth;

        return new InfoController(
            $pth["folder"]["plugins"] . "poll/",
            self::dataService(),
            new SystemChecker(),
            self::view()
        );
    }

    public static function mainAdminController(): MainAdminController
    {
        return new MainAdminController(self::dataService(), self::view());
    }

    private static function dataService(): DataService
    {
        global $pth, $sl, $cf;

        $folder = $pth["folder"]["content"];
        if ($sl !== $cf["language"]["default"]) {
            $folder = dirname($folder) . "/";
        }
        $folder .= "poll/";
        return new DataService($folder);
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;

        return new View($pth["folder"]["plugins"] . "poll/views/", $plugin_tx["poll"]);
    }
}
