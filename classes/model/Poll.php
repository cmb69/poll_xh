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

namespace Poll\Model;

class Poll
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $maxVotes;

    /** @var int Unix timestamp */
    protected $endDate;

    /** @var int */
    protected $totalVotes;

    /** @var array<string,int>*/
    protected $votes;

    public function getName(): string
    {
        return $this->name;
    }

    public function getMaxVotes(): int
    {
        return $this->maxVotes;
    }

    public function getEndDate(): int
    {
        return $this->endDate;
    }

    public function hasEnded(): bool
    {
        return $this->getEndDate() <= time();
    }

    public function getTotalVotes(): int
    {
        return $this->totalVotes;
    }

    public function getVoteCount(string $optionName): int
    {
        return $this->votes[$optionName];
    }

    /** @return array<string,int> */
    public function getVotes(): array
    {
        return $this->votes;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setMaxVotes(int $number): void
    {
        $this->maxVotes = $number;
    }

    public function setEndDate(int $timestamp): void
    {
        $this->endDate = $timestamp;
    }

    public function setTotalVotes(int $number): void
    {
        $this->totalVotes = $number;
    }

    public function increaseTotalVotes(): void
    {
        $this->totalVotes++;
    }

    public function setVoteCount(string $optionName, int $count): void
    {
        $this->votes[$optionName] = $count;
    }

    public function increaseVoteCount(string $optionName): bool
    {
        if (!isset($this->votes[$optionName])) {
            return false;
        }
        $this->votes[$optionName]++;
        return true;
    }

    public function sortVotes(): void
    {
        arsort($this->votes);
    }
}
