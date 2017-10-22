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

/*
 * Prevent direct access and usage from unsupported CMSimple_XH versions.
 */
if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.6', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Poll_XH detected an unsupported CMSimple_XH version.
Uninstall Poll_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
}

/**
 * The version number.
 */
define('POLL_VERSION', '@POLL_VERSION@');

define('POLL_TOTAL', '%%%TOTAL%%%');
define('POLL_MAX', '%%%MAX%%%');
define('POLL_END', '%%%END%%%');

/**
 * Returns the poll view or <var>false</var> in case of an invalid poll name.
 *
 * @param string $name A poll name.
 *
 * @return string (X)HTML.
 */
function poll($name)
{
    return Poll\Controller::main($name);
}

Poll\Controller::dispatch();
