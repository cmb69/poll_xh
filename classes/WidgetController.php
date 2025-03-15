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

use Pfw\View\View;
use stdClass;

class WidgetController
{
    /** @var DataService */
    private $dataService;

    /** @var View */
    private $view;

    public function __construct(DataService $dataService, View $view)
    {
        $this->dataService = $dataService;
        $this->view = $view;
    }

    public function defaultAction(string $name): string
    {
        $poll = $this->dataService->findPoll($name);
        if ($poll->hasEnded() || $this->hasVoted($name)) {
            ob_start();
            $this->prepareResultsView($poll)->render();
            return (string) ob_get_clean();
        } else {
            ob_start();
            $this->prepareVotingView($poll)->render();
            return (string) ob_get_clean();
        }
    }

    /**
     * @return bool
     */
    private function hasVoted(string $name)
    {
        if (
            isset($_COOKIE['poll_' . $name])
            && $_COOKIE['poll_' . $name] == CMSIMPLE_ROOT
        ) {
            return true;
        }
        $filename = $this->dataService->getFolder() . $name . '.ips';
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
    private function prepareVotingView(Poll $poll)
    {
        global $sn, $su;

        return $this->view
            ->template('voting')
            ->data([
                'action' => "$sn?$su",
                'name' => $poll->getName(),
                'type' => $poll->getMaxVotes() > 1 ? 'checkbox' : 'radio',
                'keys' => array_keys($poll->getVotes())
            ]);
    }

    public function voteAction(string $name): string
    {
        global $plugin_tx;

        $poll = $this->dataService->findPoll($name);
        if ($poll->hasEnded() || $this->hasVoted($name)) {
            ob_start();
            $this->prepareResultsView($poll)->render();
            return (string) ob_get_clean();
        }
        $ptx = $plugin_tx['poll'];
        if (count($_POST['poll_' . $name]) > $poll->getMaxVotes()) {
            ob_start();
            echo XH_message('fail', $ptx['error_exceeded_max'], $poll->getMaxVotes());
            $this->prepareVotingView($poll)->render();
            return (string) ob_get_clean();
        }
        $filename = $this->dataService->getFolder() . $name . '.ips';
        if (
            ($stream = fopen($filename, 'a')) !== false
            && fwrite($stream, $_SERVER['REMOTE_ADDR'] . PHP_EOL) !== false
        ) {
            setcookie('poll_' . $name, CMSIMPLE_ROOT, $poll->getEndDate());
            foreach ($_POST['poll_' . $name] as $vote) {
                $poll->increaseVoteCount($vote);
            }
            $poll->increaseTotalVotes();
            if (!$this->dataService->storePoll($name, $poll)) {
                e('cntsave', 'file', $this->dataService->getFolder() . $name . '.csv');
            }
            $err = false;
        } else {
            e('cntwriteto', 'file', $filename);
            $err = true;
        }
        if ($stream !== false) {
            fclose($stream);
        }
        if ($err) {
            ob_start();
            $this->prepareVotingView($poll)->render();
            return (string) ob_get_clean();
        } else {
            ob_start();
            echo XH_message('info', $ptx['caption_just_voted']);
            $this->prepareResultsView($poll, false)->render();
            return (string) ob_get_clean();
        }
    }

    /**
     * @param bool $msg
     * @return View
     */
    protected function prepareResultsView(Poll $poll, $msg = true)
    {
        global $admin;

        return $this->view
            ->template('results')
            ->data([
                'isAdministration' => ($admin == 'plugin_main'),
                'isFinished' => $poll->hasEnded(),
                'hasMessage' => $msg,
                'totalVotes' => $poll->getTotalVotes(),
                'votes' => $this->getVotes($poll)
            ]);
    }

    /**
     * @return list<stdClass>
     */
    private function getVotes(Poll $poll)
    {
        $votes = [];
        $poll->sortVotes();
        foreach ($poll->getVotes() as $key => $count) {
            $percentage = ($poll->getTotalVotes() == 0)
                ? 0
                : number_format(100 * $count / $poll->getTotalVotes());
            $votes[] = (object) compact('key', 'count', 'percentage');
        }
        return $votes;
    }
}
