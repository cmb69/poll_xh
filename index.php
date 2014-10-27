<?php

/**
 * Front-End of Poll_XH
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

require_once $pth['folder']['plugin_classes'] . 'Poll.php';

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
    return Poll::main($name);
}

Poll::dispatch();

?>
