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

use Plib\View;
use stdClass;

class MainAdminController
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

    public function defaultAction(): string
    {
        $o = '<div id="poll_admin">' . "\n";
        foreach ($this->dataService->getPollNames() as $name) {
            $poll = $this->dataService->findPoll($name);
            $o .= '<h1>' . $name . '</h1>' . "\n";
            $o .= $this->renderResultsView($poll);
        }
        $o .= '</div>' . "\n";
        return $o;
    }

    /**
     * @param bool $msg
     * @return string
     */
    protected function renderResultsView(Poll $poll, $msg = true)
    {
        global $admin;

        return $this->view->render("results", [
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
