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

class Poll
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $maxVotes;

    /**
     * @var int A UNIX timestamp.
     */
    protected $endDate;

    /**
     * @var int
     */
    protected $totalVotes;

    /**
     * @var array<string, int>
     */
    protected $votes;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxVotes()
    {
        return $this->maxVotes;
    }

    /**
     * @return int
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return bool
     */
    public function hasEnded()
    {
        return $this->getEndDate() <= time();
    }

    /**
     * @return int
     */
    public function getTotalVotes()
    {
        return $this->totalVotes;
    }

    /**
     * @param string $optionName
     * @return int
     */
    public function getVoteCount($optionName)
    {
        return $this->votes[$optionName];
    }

    /**
     * @return array<string, int>
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param int $number
     * @return void
     */
    public function setMaxVotes($number)
    {
        $this->maxVotes = $number;
    }

    /**
     * @param int $timestamp
     * @return void
     */
    public function setEndDate($timestamp)
    {
        $this->endDate = $timestamp;
    }

    /**
     * @param int $number
     * @return void
     */
    public function setTotalVotes($number)
    {
        $this->totalVotes = $number;
    }

    /**
     * @return void
     */
    public function increaseTotalVotes()
    {
        $this->totalVotes++;
    }

    /**
     * @param string $optionName
     * @param int $count
     * @return void
     */
    public function setVoteCount($optionName, $count)
    {
        $this->votes[$optionName] = $count;
    }

    /**
     * @param string $optionName
     * @return void
     */
    public function increaseVoteCount($optionName)
    {
        $this->votes[$optionName]++;
    }

    /**
     * @return void
     */
    public function sortVotes()
    {
        arsort($this->votes);
    }
}
