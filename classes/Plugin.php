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
        $filename = (new DataService)->getFolder() . $name . '.ips';
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

        $dataService = new DataService;
        $ptx = $plugin_tx['poll'];
        $poll = $dataService->findPoll($name);
        if (count($_POST['poll_' . $name]) > $poll->getMaxVotes()) {
            return sprintf($ptx['error_exceeded_max'], $poll->getMaxVotes())
                . self::votingView($poll);
        }
        $filename = $dataService->getFolder() . $name . '.ips';
        if (($stream = fopen($filename, 'a')) !== false
            && fwrite($stream, $_SERVER['REMOTE_ADDR'] . PHP_EOL) !== false
        ) {
            setcookie('poll_' . $name, CMSIMPLE_ROOT, $poll->getEndDate());
            foreach ($_POST['poll_' . $name] as $vote) {
                $poll->increaseVoteCount($vote);
            }
            $poll->increaseTotalVotes();
            if (!$dataService->storePoll($name, $poll)) {
                e('cntsave', 'file', $dataService->getFolder() . $name . '.csv');
            }
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
        $poll = (new DataService)->findPoll($name);
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
        $view->checks = (new SystemCheckService)->getChecks();
        return (string) $view;
    }

    /**
     * @return string
     */
    protected static function pluginAdminView()
    {
        $dataService = new DataService;
        $o = '<div id="poll_admin">' . PHP_EOL;
        foreach ($dataService->getPollNames() as $name) {
            $poll = $dataService->findPoll($name);
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
                $o .= self::aboutView();
                break;
            case 'plugin_main':
                $o .= self::pluginAdminView();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'poll');
        }
    }
}