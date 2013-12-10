<?php

/**
 * Back-End of Poll_XH
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2013 Christoph M. Becker <http://3-magi.net>
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

/**
 * Returns the plugin's about view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 *
 * @todo Fix empty elements.
 */
function Poll_aboutView()
{
    global $pth;

    $version = POLL_VERSION;
    $o = <<<EOT
<h1><a href="http://3-magi.net/?CMSimple_XH/Poll_XH">Poll_XH</a></h1>
<img src="{$pth['folder']['plugins']}poll/poll.png" width="128" height="128"
     alt="Plugin icon" class="poll_plugin_icon" />
<p style="margin-top: 1em">Version: $version</p>
<p>Copyright &copy; 2012-2013
    <a href="http://3-magi.net/">Christoph M. Becker</a></p>
<p class="poll_license">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
</p>
<p class="poll_license">
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
</p>
<p class="poll_license">
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see
    <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>

EOT;
    return $o;
}

/**
 * Returns the system check view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Poll_systemCheckView()
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '4.3.0';
    $ptx = $plugin_tx['poll'];
    $imgdir = $pth['folder']['plugins'] . 'poll/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>' . PHP_EOL
        . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
        . tag('br') . PHP_EOL;
    foreach (array('pcre') as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            . tag('br') . PHP_EOL;
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
        . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes']
        . tag('br') . tag('br') . PHP_EOL;
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding']
        . tag('br') . tag('br') . PHP_EOL;
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'poll/' . $folder;
    }
    $folders[] = Poll_dataFolder();
    foreach ($folders as $folder) {
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . tag('br') . PHP_EOL;
    }
    return $o;
}

/**
 * Returns the available polls.
 *
 * @return array
 */
function Poll_polls()
{
    $folder = Poll_dataFolder();
    $files = glob($folder . '*.csv');
    $polls = array();
    foreach ($files as $file) {
        $polls[] = basename($file, '.csv');
    }
    return $polls;
}

/**
 * Returns the plugin's main administration view.
 *
 * @return string The (X)HTML.
 */
function Poll_pluginAdminView()
{
    $o = '<div id="poll_admin">' . PHP_EOL;
    foreach (Poll_polls() as $poll) {
        $o .= '<h1>' . $poll . '</h1>' . PHP_EOL
            . Poll_resultsView($poll) . PHP_EOL;
    }
    $o .= '</div>' . PHP_EOL;
    return $o;
}

/**
 * Handle the plugin administration.
 */
if (isset($poll) && $poll == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= Poll_aboutView() . tag('hr') . Poll_systemCheckView();
        break;
    case 'plugin_main':
        $o .= Poll_pluginAdminView();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
