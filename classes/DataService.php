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

class DataService
{
    private const TOTAL = '%%%TOTAL%%%';

    private const MAX = '%%%MAX%%%';

    private const END = '%%%END%%%';

    /** @var string */
    private $folder;

    public function __construct(string $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param string $name
     * @return Poll
     */
    public function findPoll($name)
    {
        $filename = $this->getFolder() . $name . '.csv';
        $poll = new Poll();
        $poll->setName($name);
        $poll->setMaxVotes(1);
        $poll->setEndDate(2147483647);
        $poll->setTotalVotes(0);
        $lines = file($filename);
        if ($lines !== false) {
            foreach ($lines as $line) {
                $record = explode("\t", rtrim($line));
                switch ($record[0]) {
                    case self::MAX:
                        $poll->setMaxVotes((int) $record[1]);
                        break;
                    case self::END:
                        $poll->setEndDate($record[1] <= PHP_INT_MAX ? (int) $record[1] : PHP_INT_MAX);
                        break;
                    case self::TOTAL:
                        $poll->setTotalVotes((int) $record[1]);
                        break;
                    default:
                        $poll->setVoteCount($record[0], isset($record[1]) ? (int) $record[1] : 0);
                }
            }
        }
        return $poll;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function storePoll($name, Poll $poll)
    {
        $filename = $this->getFolder() . $name . '.csv';
        $lines = array();
        foreach ($poll->getVotes() as $key => $count) {
            $lines[] = $key . "\t" . $count;
        }
        $lines[] = self::MAX . "\t" . $poll->getMaxVotes();
        $lines[] = self::END . "\t" . $poll->getEndDate();
        $lines[] = self::TOTAL . "\t" . $poll->getTotalVotes();
        return file_put_contents($filename, implode("\n", $lines) . "\n") !== false;
    }

    /**
     * @return list<string>
     */
    public function getPollNames()
    {
        $folder = $this->getFolder();
        $files = scandir($folder);
        $polls = array();
        if ($files === false) {
            return $polls;
        }
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === "csv") {
                $polls[] = basename($file, '.csv');
            }
        }
        return $polls;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        if (!file_exists($this->folder)) {
            mkdir($this->folder, 0777, true);
            chmod($this->folder, 0777);
        }
        return $this->folder;
    }
}
