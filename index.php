<?php

/**
 * Front-End of Poll_XH
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
 * The version number.
 */
define('POLL_VERSION', '@POLL_VERSION@');

define('POLL_TOTAL', '%%%TOTAL%%%');
define('POLL_MAX', '%%%MAX%%%');
define('POLL_END', '%%%END%%%');

/**
 * Returns a language string depending on the number.
 *
 * @param string $key   A base key.
 * @param int    $count A number.
 *
 * @return string
 *
 * @global array The localization of the plugins.
 */
function Poll_number($key, $count)
{
    global $plugin_tx;

    if ($count == 1) {
        $suffix = '_singular';
    } elseif ($count >= 2 && $count <= 4) {
        $suffix = '_plural_2-4';
    } else {
        $suffix = '_plural_5';
    }
    return $plugin_tx['poll'][$key . $suffix];
}

/**
 * Returns the path to the data folder.
 *
 * @return string
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the plugins.
 */
function Poll_dataFolder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['poll'];
    if ($pcf['folder_data'] == '') {
        $folder = $pth['folder']['plugins'] . 'poll/data/';
    } else {
        $folder = $pth['folder']['base'] . $pcf['folder_data'];
    }
    if (substr($folder, -1) != '/') {
        $folder .= '/';
    }
    if (file_exists($folder)) {
        if (!is_dir($folder)) {
            e('cntopen', 'folder', $folder);
        }
    } else {
        if (!mkdir($folder, 0777, true)) {
            e('cntwriteto', 'folder', $folder);
        }
    }
    return $folder;
}

/**
 * Reads or writes a poll, and returns its data.
 *
 * The function caches the data of a single poll internally.
 *
 * @param string $name    A poll name.
 * @param array  $newData New poll data.
 *
 * @return array
 */
function Poll_data($name, $newData = null)
{
    static $cname = null, $data = null;

    $filename = Poll_dataFolder() . $name . '.csv';
    if (!isset($newData)) {
        if (!isset($data) || $name != $cname) {
            $cname = $name;
            $data = array('max' => 1, 'end' => 2147483647, 'total' => 0);
            $lines = file($filename);
            if ($lines !== false) {
                foreach ($lines as $line) {
                    $record = explode("\t", rtrim($line));
                    switch ($record[0]) {
                    case POLL_MAX:
                        $data['max'] = $record[1];
                        break;
                    case POLL_END:
                        $data['end'] = $record[1];
                        break;
                    case POLL_TOTAL:
                        $data['total'] = $record[1];
                        break;
                    default:
                        $data['votes'][$record[0]]
                            = isset($record[1]) ? $record[1] : 0;
                    }
                }
            }
        }
    } else {
        $cname = $name;
        $data = $newData;
        $lines = array();
        foreach ($data['votes'] as $key => $count) {
            $lines[] = $key . "\t" . $count;
        }
        $lines[] = POLL_MAX . "\t" . $data['max'];
        $lines[] = POLL_END . "\t" . $data['end'];
        $lines[] = POLL_TOTAL . "\t" . $data['total'];
        if (($stream = fopen($filename, 'w')) === false
            || fwrite($stream, implode(PHP_EOL, $lines) . PHP_EOL) === false
        ) {
            e('cntsave', 'file', $filename);
        }
        if ($stream !== false) {
            fclose($stream);
        }
    }
    return $data;
}

/**
 * Returns whether the poll has ended.
 *
 * @param string $name A poll name.
 *
 * @return bool
 */
function Poll_hasEnded($name)
{
    $data = Poll_data($name);
    return $data['end'] <= time();
}

/**
 * Returns whether the current user has already voted.
 *
 * The current user is identified by a cookie and by his IP address.
 *
 * @param string $name A poll name.
 *
 * @return bool
 */
function Poll_hasVoted($name)
{
    if (isset($_COOKIE['poll_' . $name])
        && $_COOKIE['poll_' . $name] == CMSIMPLE_ROOT
    ) {
        return true;
    }
    $filename = Poll_dataFolder() . $name . '.ips';
    if (!file_exists($filename)) {
        touch($filename);
    }
    $lines = file($filename);
    if ($lines === false) {
        e('cntopen', 'file', $filename);
        return false;
    }
    $ips = array_map('rtrim', $lines);
    return in_array($_SERVER['REMOTE_ADDR'], $ips);
}

/**
 * Returns whether there's a submitted vote for the given poll.
 *
 * @param string $name A poll name.
 *
 * @return bool
 */
function Poll_isVoting($name)
{
    return isset($_POST['poll_' . $name]);
}

/**
 * Registers the new vote and returns the result view.
 *
 * @param string $name A poll name.
 *
 * @return string (X)HTML.
 *
 * @global array The localization of the plugins.
 */
function Poll_vote($name)
{
    global $plugin_tx;

    $ptx = $plugin_tx['poll'];
    $data = Poll_data($name);
    if (count($_POST['poll_' . $name]) > $data['max']) {
        return sprintf($ptx['error_exceeded_max'], $data['max'])
            . Poll_votingView($name);
    }
    $filename = Poll_dataFolder() . $name . '.ips';
    if (($stream = fopen($filename, 'a')) !== false
        && fwrite($stream, $_SERVER['REMOTE_ADDR'] . PHP_EOL) !== false
    ) {
        setcookie('poll_' . $name, CMSIMPLE_ROOT, $data['end']);
        foreach ($_POST['poll_' . $name] as $vote) {
            $data['votes'][stsl($vote)]++;
        }
        $data['total']++;
        Poll_data($name, $data);
        $err = false;
    } else {
        e('cntwriteto', 'file', $filename);
        $err = true;
    }
    if ($stream !== false) {
        fclose($stream);
    }
    return $err
        ? Poll_votingView($name)
        : $ptx['caption_just_voted'] . Poll_resultsView($name, false);
}

/**
 * Returns the voting view.
 *
 * @param string $name A poll name.
 *
 * @return string (X)HTML.
 *
 * @global string The script name.
 * @global string The current page URL.
 * @global array  The localization of the plugins.
 *
 * @todo Fix empty elements.
 */
function Poll_votingView($name)
{
    global $sn, $su, $plugin_tx;

    $ptx = $plugin_tx['poll'];
    $data = Poll_data($name);
    $type = $data['max'] > 1 ? 'checkbox' : 'radio';
    $o = <<<EOT
<form class="poll" action="$sn?$su" method="post">
    $ptx[caption_vote]
    <ul>

EOT;
    $i = 0;
    foreach ($data['votes'] as $key => $dummy) {
        $key = htmlspecialchars($key, ENT_COMPAT, 'UTF-8');
        $o .= <<<EOT
        <li>
            <input type="$type" id="poll_$name$i" name="poll_${name}[]"
                   value="$key" />
            <label for="poll_$name$i">$key</label>
        </li>

EOT;
        $i++;
    }
    $o .= <<<EOT
    </ul>
    <input type="submit" value="$ptx[label_vote]" />
</form>

EOT;
    return $o;
}

/**
 * Returns the results view.
 *
 * @param string $name A poll name.
 * @param bool   $msg  Whether the caption_voted should be displayed.
 *
 * @return string (X)HTML.
 *
 * @global string The value of the admin parameter.
 * @global array  The localization of the core.
 */
function Poll_resultsView($name, $msg = true)
{
    global $admin, $plugin_tx;

    $ptx = $plugin_tx['poll'];
    $data = Poll_data($name);
    $o = '';
    if ($admin != 'plugin_main') {
        if (Poll_hasEnded($name)) {
            $o .= $ptx['caption_ended'] . PHP_EOL;
        } elseif ($msg) {
            $o .= $ptx['caption_voted'] . PHP_EOL;
        }
        $o .= $ptx['caption_results'] . PHP_EOL;
    }
    $o .= '<ul class="poll_results">' . PHP_EOL;
    arsort($data['votes']);
    foreach ($data['votes'] as $key => $count) {
        $percentage = ($data['total'] == 0)
            ? 0
            : 100 * $count / $data['total'];
        $result = sprintf(
            Poll_number('label_result', $count),
            htmlspecialchars($key, ENT_COMPAT, 'UTF-8'),
            $percentage, $count
        );
        $o .= <<<EOT
    <li>
        <div class="poll_results">$result</div>
        <div class="poll_bar" style="width: $percentage%">&nbsp;</div>
    </li>

EOT;
    }
    $o .= '</ul>' . PHP_EOL
        . sprintf(Poll_number('caption_total', $data['total']), $data['total'])
        . PHP_EOL;
    return $o;
}

/**
 * Returns the poll view or <var>false</var> in case of an invalid poll name.
 *
 * @param string $name A poll name.
 *
 * @return string (X)HTML.
 *
 * @global string The document fragment containing error messages.
 * @global array  The localization of the plugins.
 *
 * @access public
 */
function poll($name)
{
    global $e, $plugin_tx;

    if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
        $e = '<li><b>'
            . sprintf($plugin_tx['poll']['error_invalid_name'], $name)
            . '</b></li>' . PHP_EOL;
        return false;
    }
    $o = '';
    if (Poll_hasEnded($name) || Poll_hasVoted($name)) {
        $o .= Poll_resultsView($name);
    } elseif (Poll_isVoting($name)) {
        $o .= Poll_vote($name);
    } else {
        $o .= Poll_votingView($name);
    }
    return $o;
}

?>
