<?php

/**
 * The controller.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
 */

namespace Poll;

/**
 * The controller.
 *
 * @category CMSimple_XH
 * @package  Poll
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Poll_XH
 */
class Controller
{
    /**
     * Dispatches on plugin related requests.
     *
     * @return void
     */
    public static function dispatch()
    {
        if (XH_ADM && XH_wantsPluginAdministration('poll')) {
            self::handleAdministration();
        }
    }

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
    protected static function number($key, $count)
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
    protected static function dataFolder()
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
     * @param string    $name    A poll name.
     * @param Poll $newPoll A new poll.
     *
     * @return Poll
     */
    protected static function data($name, $newPoll = null)
    {
        static $cname = null, $poll = null;

        $filename = self::dataFolder() . $name . '.csv';
        if (!isset($newPoll)) {
            if (!isset($poll) || $name != $cname) {
                $cname = $name;
                $poll = new Poll();
                $poll->setName($name);
                $poll->setMaxVotes(1);
                $poll->setEndDate(2147483647);
                $poll->setTotalVotes(0);
                $lines = file($filename);
                if ($lines !== false) {
                    foreach ($lines as $line) {
                        $record = explode("\t", rtrim($line));
                        switch ($record[0]) {
                        case POLL_MAX:
                            $poll->setMaxVotes($record[1]);
                            break;
                        case POLL_END:
                            $poll->setEndDate($record[1]);
                            break;
                        case POLL_TOTAL:
                            $poll->setTotalVotes($record[1]);
                            break;
                        default:
                            $poll->setVoteCount(
                                $record[0], isset($record[1]) ? $record[1] : 0
                            );
                        }
                    }
                }
            }
        } else {
            $cname = $name;
            $poll = $newPoll;
            $lines = array();
            foreach ($poll->getVotes() as $key => $count) {
                $lines[] = $key . "\t" . $count;
            }
            $lines[] = POLL_MAX . "\t" . $poll->getMaxVotes();
            $lines[] = POLL_END . "\t" . $poll->getEndDate();
            $lines[] = POLL_TOTAL . "\t" . $poll->getTotalVotes();
            if (($stream = fopen($filename, 'w')) === false
                || fwrite($stream, implode(PHP_EOL, $lines) . PHP_EOL) === false
            ) {
                e('cntsave', 'file', $filename);
            }
            if ($stream !== false) {
                fclose($stream);
            }
        }
        return $poll;
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
    protected static function hasVoted($name)
    {
        if (isset($_COOKIE['poll_' . $name])
            && $_COOKIE['poll_' . $name] == CMSIMPLE_ROOT
        ) {
            return true;
        }
        $filename = self::dataFolder() . $name . '.ips';
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
    protected static function isVoting($name)
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
    protected static function vote($name)
    {
        global $plugin_tx;

        $ptx = $plugin_tx['poll'];
        $poll = self::data($name);
        if (count($_POST['poll_' . $name]) > $poll->getMaxVotes()) {
            return sprintf($ptx['error_exceeded_max'], $poll->getMaxVotes())
                . self::votingView($poll);
        }
        $filename = self::dataFolder() . $name . '.ips';
        if (($stream = fopen($filename, 'a')) !== false
            && fwrite($stream, $_SERVER['REMOTE_ADDR'] . PHP_EOL) !== false
        ) {
            setcookie('poll_' . $name, CMSIMPLE_ROOT, $poll->getEndDate());
            foreach ($_POST['poll_' . $name] as $vote) {
                $poll->increaseVoteCount(stsl($vote));
            }
            $poll->increaseTotalVotes();
            self::data($name, $poll);
            $err = false;
        } else {
            e('cntwriteto', 'file', $filename);
            $err = true;
        }
        if ($stream !== false) {
            fclose($stream);
        }
        return $err
            ? self::votingView($poll)
            : $ptx['caption_just_voted'] . self::resultsView($poll, false);
    }

    /**
     * Returns the voting view.
     *
     * @param Poll $poll A poll.
     *
     * @return string (X)HTML.
     *
     * @global string The script name.
     * @global string The current page URL.
     * @global array  The localization of the plugins.
     *
     * @todo Fix empty elements.
     */
    protected static function votingView(Poll $poll)
    {
        global $sn, $su, $plugin_tx;

        $ptx = $plugin_tx['poll'];
        $name = $poll->getName();
        $type = $poll->getMaxVotes() > 1 ? 'checkbox' : 'radio';
        $o = <<<EOT
<form class="poll" action="$sn?$su" method="post">
    $ptx[caption_vote]
    <ul>

EOT;
        $i = 0;
        foreach ($poll->getVotes() as $key => $dummy) {
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
     * @param Poll $poll A poll.
     * @param bool      $msg  Whether the caption_voted should be displayed.
     *
     * @return string (X)HTML.
     *
     * @global string The value of the admin parameter.
     * @global array  The localization of the core.
     */
    protected static function resultsView(Poll $poll, $msg = true)
    {
        global $admin, $plugin_tx;

        $ptx = $plugin_tx['poll'];
        $o = '';
        if ($admin != 'plugin_main') {
            if ($poll->hasEnded()) {
                $o .= $ptx['caption_ended'] . PHP_EOL;
            } elseif ($msg) {
                $o .= $ptx['caption_voted'] . PHP_EOL;
            }
            $o .= $ptx['caption_results'] . PHP_EOL;
        }
        $o .= '<ul class="poll_results">' . PHP_EOL;
        $poll->sortVotes();
        foreach ($poll->getVotes() as $key => $count) {
            $percentage = ($poll->getTotalVotes() == 0)
                ? 0
                : 100 * $count / $poll->getTotalVotes();
            $result = sprintf(
                self::number('label_result', $count),
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
            . sprintf(
                self::number('caption_total', $poll->getTotalVotes()),
                $poll->getTotalVotes()
            )
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
        $poll = self::data($name);
        if ($poll->hasEnded() || self::hasVoted($name)) {
            $o .= self::resultsView($poll);
        } elseif (self::isVoting($name)) {
            $o .= self::vote($name);
        } else {
            $o .= self::votingView($poll);
        }
        return $o;
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
    protected static function aboutView()
    {
        global $pth;

        $version = POLL_VERSION;
        $o = <<<EOT
<h1><a href="http://3-magi.net/?CMSimple_XH/Poll_XH">Poll_XH</a></h1>
<img src="{$pth['folder']['plugins']}poll/poll.png" width="128" height="128"
     alt="Plugin icon" class="poll_plugin_icon" />
<p style="margin-top: 1em">Version: $version</p>
<p>Copyright &copy; 2012-2017
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
    protected static function systemCheckView()
    {
        global $pth, $tx, $plugin_tx;

        $phpVersion = '5.1.2';
        $ptx = $plugin_tx['poll'];
        $imgdir = $pth['folder']['plugins'] . 'poll/images/';
        $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
        $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
        $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
        $o = '<h4>' . $ptx['syscheck_title'] . '</h4>' . PHP_EOL
            . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
            . tag('br') . PHP_EOL;
        foreach (array() as $ext) {
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
        $folders[] = self::dataFolder();
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
    protected static function polls()
    {
        $folder = self::dataFolder();
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
    protected static function pluginAdminView()
    {
        $o = '<div id="poll_admin">' . PHP_EOL;
        foreach (self::polls() as $name) {
            $poll = self::data($name);
            $o .= '<h1>' . $name . '</h1>' . PHP_EOL
                . self::resultsView($poll) . PHP_EOL;
        }
        $o .= '</div>' . PHP_EOL;
        return $o;
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The value of the admin GP parameter.
     * @global string The value of the action GP parameter.
     * @global string The (X)HTML fragment of the contents area.
     */
    protected static function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
        case '':
            $o .= self::aboutView() . tag('hr') . self::systemCheckView();
            break;
        case 'plugin_main':
            $o .= self::pluginAdminView();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'poll');
        }
    }
}

?>
