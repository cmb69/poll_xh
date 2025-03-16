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

use Plib\Request;
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

    public function __invoke(Request $request, string $name): Response
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
            return Response::create($this->view->message("fail", "error_invalid_name", $name));
        }
        if ($request->postArray("poll_$name") !== null) {
            return $this->voteAction($request, $name);
        } else {
            return $this->defaultAction($request, $name);
        }
    }

    private function defaultAction(Request $request, string $name): Response
    {
        $poll = $this->dataService->findPoll($name);
        if ($poll->hasEnded() || $this->hasVoted($request, $name)) {
            return Response::create($this->view->render("results", $this->resultData($request, $poll)));
        } else {
            return Response::create($this->view->render("voting", $this->votingData($request, $poll)));
        }
    }

    private function hasVoted(Request $request, string $name): bool
    {
        if ($request->cookie("poll_$name") !== null) {
            return true;
        }
        return $this->dataService->isVoteRegistered($name, $request->remoteAddr());
    }

    /** @return array<string,mixed> */
    private function votingData(Request $request, Poll $poll): array
    {
        return [
            'action' => $request->url()->relative(),
            'name' => $poll->getName(),
            'type' => $poll->getMaxVotes() > 1 ? 'checkbox' : 'radio',
            'keys' => array_keys($poll->getVotes())
        ];
    }

    private function voteAction(Request $request, string $name): Response
    {
        $poll = $this->dataService->findPoll($name);
        if ($poll->hasEnded() || $this->hasVoted($request, $name)) {
            return Response::create($this->view->render("results", $this->resultData($request, $poll)));
        }
        $votes = $request->postArray("poll_$name");
        assert($votes !== null);
        if (count($votes) > $poll->getMaxVotes()) {
            return Response::create(
                $this->view->message('fail', 'error_exceeded_max', $poll->getMaxVotes())
                . $this->view->render("voting", $this->votingData($request, $poll))
            );
        }
        if ($this->dataService->registerVote($name, $request->remoteAddr())) {
            foreach ($votes as $vote) {
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
                 . $this->view->render("voting", $this->votingData($request, $poll))
            );
        } else {
            return Response::create(
                $this->view->message('info', 'caption_just_voted')
                . $this->view->render("results", $this->resultData($request, $poll, false))
            )->withCookie('poll_' . $name, CMSIMPLE_ROOT, $poll->getEndDate());
        }
    }

    /** @return array<string,mixed> */
    protected function resultData(Request $request, Poll $poll, bool $msg = true): array
    {
        return [
            'isAdministration' => $request->get("admin") === "plugin_main",
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
