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
use Pfw\SystemCheckService;

class InfoController
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth;

        (new View('poll'))
            ->template('info')
            ->data([
                'logo' => "{$pth['folder']['plugins']}poll/poll.png",
                'version' => Plugin::VERSION,
                'checks' => (new SystemCheckService())
                    ->minPhpVersion('7.1.0')
                    ->minXhVersion('1.7.0')
                    ->minPfwVersion('0.2.0')
                    ->writable("{$pth['folder']['plugins']}poll/css")
                    ->writable("{$pth['folder']['plugins']}poll/languages")
                    ->writable((new DataService())->getFolder())
                    ->getChecks()
            ])
            ->render();
    }
}
