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

use Pfw\SystemCheckService;
use Plib\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var DataService */
    private $dataService;

    /** @var SystemCheckService */
    private $systemCheckService;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        DataService $dataService,
        SystemCheckService $systemCheckService,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->dataService = $dataService;
        $this->systemCheckService = $systemCheckService;
        $this->view = $view;
    }

    public function defaultAction(): string
    {
        return $this->view->render("info", [
            'logo' => $this->pluginFolder . "poll.png",
            'version' => Plugin::VERSION,
            'checks' => $this->systemCheckService
                ->minPhpVersion('7.1.0')
                ->minXhVersion('1.7.0')
                ->minPfwVersion('0.2.0')
                ->writable($this->pluginFolder . "css")
                ->writable($this->pluginFolder . "languages")
                ->writable($this->dataService->getFolder())
                ->getChecks()
        ]);
    }
}
