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

class WidgetController
{
    /**
     * @string
     */
    private $name;

    /**
     * @Poll
     */
    private $poll;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->poll = (new DataService)->findPoll($name);
    }

    /**
     * @return void
     */
    public function defaultAction()
    {

        if ($this->poll->hasEnded() || $this->hasVoted()) {
            echo $this->prepareResultsView();
        } else {
            echo $this->prepareVotingView();
        }
    }

    /**
     * @return bool
     */
    private function hasVoted()
    {
        if (isset($_COOKIE['poll_' . $this->name])
            && $_COOKIE['poll_' . $this->name] == CMSIMPLE_ROOT
        ) {
            return true;
        }
        $filename = (new DataService)->getFolder() . $this->name . '.ips';
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
     * @return View
     */
    private function prepareVotingView()
    {
        global $sn, $su;

        $view = new View('voting');
        $view->action = "$sn?$su";
        $view->name = $this->poll->getName();
        $view->type = $this->poll->getMaxVotes() > 1 ? 'checkbox' : 'radio';
        $view->keys = array_keys($this->poll->getVotes());
        return $view;
    }

    /**
     * @return void
     */
    public function voteAction()
    {
        global $plugin_tx;

        if ($this->poll->hasEnded() || $this->hasVoted()) {
            echo $this->prepareResultsView();
            return;
        }
        $dataService = new DataService;
        $ptx = $plugin_tx['poll'];
        if (count($_POST['poll_' . $this->name]) > $this->poll->getMaxVotes()) {
            echo sprintf($ptx['error_exceeded_max'], $this->poll->getMaxVotes())
                . $this->prepareVotingView();
            return;
        }
        $filename = $dataService->getFolder() . $this->name . '.ips';
        if (($stream = fopen($filename, 'a')) !== false
            && fwrite($stream, $_SERVER['REMOTE_ADDR'] . PHP_EOL) !== false
        ) {
            setcookie('poll_' . $this->name, CMSIMPLE_ROOT, $this->poll->getEndDate());
            foreach ($_POST['poll_' . $this->name] as $vote) {
                $this->poll->increaseVoteCount($vote);
            }
            $this->poll->increaseTotalVotes();
            if (!$dataService->storePoll($this->name, $this->poll)) {
                e('cntsave', 'file', $dataService->getFolder() . $this->name . '.csv');
            }
            $err = false;
        } else {
            e('cntwriteto', 'file', $filename);
            $err = true;
        }
        if ($stream !== false) {
            fclose($stream);
        }
        echo $err
            ? $this->prepareVotingView()
            : $ptx['caption_just_voted'] . $this->prepareResultsView(false);
    }

    /**
     * @param bool $msg
     * @return View
     */
    protected function prepareResultsView($msg = true)
    {
        global $admin;

        $view = new View('results');
        $view->isAdministration = ($admin == 'plugin_main');
        $view->isFinished = $this->poll->hasEnded();
        $view->msg = $msg;
        $view->totalVotes = $this->poll->getTotalVotes();
        $view->votes = $this->getVotes($this->poll);
        return $view;
    }

    /**
     * @return stdClass
     */
    private function getVotes(Poll $poll)
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
}