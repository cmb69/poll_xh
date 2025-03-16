<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Plib\Response;
use Plib\View;
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

    public function __invoke(string $name): Response
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
            return Response::create($this->view->message("fail", "error_invalid_name", $name));
        }
        if (isset($_POST['poll_' . $name])) {
            return $this->voteAction($name);
        } else {
            return $this->defaultAction($name);
        }
    }

    private function defaultAction(string $name): Response
    {
        $poll = $this->dataService->findPoll($name);
        if ($poll->hasEnded() || $this->hasVoted($name)) {
            return Response::create($this->view->render("results", $this->resultData($poll)));
        } else {
            return Response::create($this->view->render("voting", $this->votingData($poll)));
        }
    }

    private function hasVoted(string $name): bool
    {
        if (
            isset($_COOKIE['poll_' . $name])
            && $_COOKIE['poll_' . $name] == CMSIMPLE_ROOT
        ) {
            return true;
        }
        return $this->dataService->isVoteRegistered($name, $_SERVER["REMOTE_ADDR"]);
    }

    /** @return array<string,mixed> */
    private function votingData(Poll $poll): array
    {
        global $sn, $su;

        return [
            'action' => "$sn?$su",
            'name' => $poll->getName(),
            'type' => $poll->getMaxVotes() > 1 ? 'checkbox' : 'radio',
            'keys' => array_keys($poll->getVotes())
        ];
    }

    private function voteAction(string $name): Response
    {
        $poll = $this->dataService->findPoll($name);
        if ($poll->hasEnded() || $this->hasVoted($name)) {
            return Response::create($this->view->render("results", $this->resultData($poll)));
        }
        if (count($_POST['poll_' . $name]) > $poll->getMaxVotes()) {
            return Response::create(
                $this->view->message('fail', 'error_exceeded_max', $poll->getMaxVotes())
                . $this->view->render("voting", $this->votingData($poll))
            );
        }
        if ($this->dataService->registerVote($name, $_SERVER["REMOTE_ADDR"])) {
            foreach ($_POST['poll_' . $name] as $vote) {
                $poll->increaseVoteCount($vote);
            }
            $poll->increaseTotalVotes();
            if (!$this->dataService->storePoll($name, $poll)) {
                $err = true;
            } else {
                $err = false;
            }
        } else {
            $err = true;
        }
        if ($err) {
            return Response::create(
                $this->view->message("fail", "error_save")
                 . $this->view->render("voting", $this->votingData($poll))
            );
        } else {
            return Response::create(
                $this->view->message('info', 'caption_just_voted')
                . $this->view->render("results", $this->resultData($poll, false))
            )->withCookie('poll_' . $name, CMSIMPLE_ROOT, $poll->getEndDate());
        }
    }

    /** @return array<string,mixed> */
    protected function resultData(Poll $poll, bool $msg = true): array
    {
        global $admin;

        return [
            'isAdministration' => ($admin == 'plugin_main'),
            'isFinished' => $poll->hasEnded(),
            'hasMessage' => $msg,
            'totalVotes' => $poll->getTotalVotes(),
            'votes' => $this->getVotes($poll)
        ];
    }

    /** @return list<stdClass> */
    private function getVotes(Poll $poll): array
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
