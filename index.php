<?php

/**
 * Front-End of Poll_XH
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
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
 * Autoloads a plugin class.
 *
 * @param string $class A class name.
 *
 * @return void
 *
 * @global array The paths of system files and folders.
 */
function Poll_autoload($class)
{
    global $pth;

    $parts = explode('_', $class, 2);
    if ($parts[0] == 'Poll') {
        include_once $pth['folder']['plugins'] . 'poll/classes/'
            . $parts[1] . '.php';
    }
}

spl_autoload_register('Poll_autoload');

/**
 * Returns the poll view or <var>false</var> in case of an invalid poll name.
 *
 * @param string $name A poll name.
 *
 * @return string (X)HTML.
 */
function poll($name)
{
    return Poll_Controller::main($name);
}

Poll_Controller::dispatch();

?>
