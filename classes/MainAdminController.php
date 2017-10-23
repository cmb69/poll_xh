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

class MainAdminController extends Controller
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        $dataService = new DataService;
        $o = '<div id="poll_admin">' . PHP_EOL;
        foreach ($dataService->getPollNames() as $name) {
            $poll = $dataService->findPoll($name);
            $o .= '<h1>' . $name . '</h1>' . PHP_EOL
                . $this->prepareResultsView($poll) . PHP_EOL;
        }
        $o .= '</div>' . PHP_EOL;
        echo $o;
    }
}
