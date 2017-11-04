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

use Pfw\View\HtmlView;

class WidgetController extends Controller
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
            $this->prepareResultsView($this->poll)->render();
        } else {
            $this->prepareVotingView()->render();
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

        return (new HtmlView('poll'))
            ->template('voting')
            ->data([
                'action' => "$sn?$su",
                'name' => $this->poll->getName(),
                'type' => $this->poll->getMaxVotes() > 1 ? 'checkbox' : 'radio',
                'keys' => array_keys($this->poll->getVotes())
            ]);
    }

    /**
     * @return void
     */
    public function voteAction()
    {
        global $plugin_tx;

        if ($this->poll->hasEnded() || $this->hasVoted()) {
            $this->prepareResultsView($this->poll)->render();
            return;
        }
        $dataService = new DataService;
        $ptx = $plugin_tx['poll'];
        if (count($_POST['poll_' . $this->name]) > $this->poll->getMaxVotes()) {
            echo sprintf($ptx['error_exceeded_max'], $this->poll->getMaxVotes());
            $this->prepareVotingView()->render();
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
        if ($err) {
            $this->prepareVotingView()->render();
        } else {
            echo $ptx['caption_just_voted'];
            $this->prepareResultsView($this->poll, false)->render();
        }
    }
}
