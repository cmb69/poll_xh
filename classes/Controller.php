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

abstract class Controller
{
    /**
     * @param bool $msg
     * @return View
     */
    protected function prepareResultsView(Poll $poll, $msg = true)
    {
        global $admin;

        return (new View('poll'))
            ->template('results')
            ->data([
                'isAdministration' => ($admin == 'plugin_main'),
                'isFinished' => $poll->hasEnded(),
                'hasMessage' => $msg,
                'totalVotes'=> $poll->getTotalVotes(),
                'votes' => $this->getVotes($poll)
            ]);
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
                : number_format(100 * $count / $poll->getTotalVotes());
            $votes[] = (object) compact('key', 'count', 'percentage');
        }
        return $votes;
    }
}
