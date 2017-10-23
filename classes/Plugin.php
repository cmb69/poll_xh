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
    /**
     * @return void
     */
    public static function dispatch()
    {
        if (XH_ADM && XH_wantsPluginAdministration('poll')) {
            self::handleAdministration();
        }
    }

    /**
     * @return string
     */
    protected static function dataFolder()
    {
        global $pth, $sl, $cf;

        $folder = $pth['folder']['content'];
        if ($sl !== $cf['language']['default']) {
            $folder = dirname($folder);
        }
        $folder .= 'poll/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
            chmod($folder, 0777);
        }
        return $folder;
    }

    /**
     * @param string $name
     * @return Poll
     */
    protected static function data($name, Poll $newPoll = null)
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
                                $poll->setVoteCount($record[0], isset($record[1]) ? $record[1] : 0);
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
     * @param string $name
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
     * @param string $name
     * @return bool
     */
    protected static function isVoting($name)
    {
        return isset($_POST['poll_' . $name]);
    }

    /**
     * @param string $name
     * @return string
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
                $poll->increaseVoteCount($vote);
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
     * @return string
     */
    protected static function votingView(Poll $poll)
    {
        global $sn, $su;

        $view = new View('voting');
        $view->action = "$sn?$su";
        $view->name = $poll->getName();
        $view->type = $poll->getMaxVotes() > 1 ? 'checkbox' : 'radio';
        $view->keys = array_keys($poll->getVotes());
        return (string) $view;
    }

    /**
     * @param bool $msg
     * @return string
     */
    protected static function resultsView(Poll $poll, $msg = true)
    {
        global $admin;

        $view = new View('results');
        $view->isAdministration = ($admin == 'plugin_main');
        $view->isFinished = $poll->hasEnded();
        $view->msg = $msg;
        $view->totalVotes = $poll->getTotalVotes();
        $view->votes = self::getVotes($poll);
        return (string) $view;
    }

    /**
     * @return stdClass
     */
    private static function getVotes(Poll $poll)
    {
        $votes = [];
        $poll->sortVotes();
        foreach ($poll->getVotes() as $key => $count) {
            $percentage = ($poll->getTotalVotes() == 0)
                ? 0
                : 100 * $count / $poll->getTotalVotes();
            $votes[] = (object) compact('key', 'count', 'percentage');
        }
        return $votes;
    }

    /**
     * @param string $name
     * @return string
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
     * @return string
     */
    protected static function aboutView()
    {
        global $pth;

        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}poll/poll.png";
        $view->version = POLL_VERSION;
        return (string) $view;
    }

    /**
     * @return string
     */
    protected static function systemCheckView()
    {
        global $pth, $plugin_tx;

        $phpVersion = '5.4.0';
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
        foreach (array('css/', 'languages/') as $folder) {
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
     * @return string
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
     * @return void
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
