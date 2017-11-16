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
    const TOTAL = '%%%TOTAL%%%';

    const MAX = '%%%MAX%%%';

    const END = '%%%END%%%';

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
                        $poll->setMaxVotes($record[1]);
                        break;
                    case self::END:
                        $poll->setEndDate($record[1]);
                        break;
                    case self::TOTAL:
                        $poll->setTotalVotes($record[1]);
                        break;
                    default:
                        $poll->setVoteCount($record[0], isset($record[1]) ? $record[1] : 0);
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
        return XH_writeFile($filename, implode(PHP_EOL, $lines) . PHP_EOL) !== false;
    }

    /**
     * @return array
     */
    public function getPollNames()
    {
        $folder = $this->getFolder();
        $files = glob($folder . '*.csv');
        $polls = array();
        foreach ($files as $file) {
            $polls[] = basename($file, '.csv');
        }
        return $polls;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        global $pth, $sl, $cf;

        $folder = $pth['folder']['content'];
        if ($sl !== $cf['language']['default']) {
            $folder = dirname($folder);
        }
        $folder .= 'poll/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
            chmod($folder, 0777);
        }
        return $folder;
    }
}
