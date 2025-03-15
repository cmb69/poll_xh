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

/**
 * @param string $name
 * @return string
 */
function poll($name)
{
    global $e, $plugin_tx;

    if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
        $e = '<li><b>'
            . sprintf($plugin_tx['poll']['error_invalid_name'], $name)
            . '</b></li>' . PHP_EOL;
        return '';
    }
    $controller = new Poll\WidgetController($name);
    ob_start();
    if (isset($_POST['poll_' . $name])) {
        $controller->voteAction();
    } else {
        $controller->defaultAction();
    }
    return (string) ob_get_clean();
}

(new Poll\Plugin)->run();
