<?php

/**
 * Copyright 2014-2017 Christoph M. Becker
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

use PHPUnit\Framework\TestCase;

require_once './classes/Poll.php';

/**
 * Testing the polls.
 *
 * @category Testing
 * @package  Poll
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Poll_XH
 */
class PollTest extends TestCase
{
    /**
     * The test subject.
     *
     * @var Poll
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->subject = new Poll();
    }

    /**
     * Tests that the poll has ended.
     *
     * @return void
     */
    public function testHasEnded()
    {
        $this->subject->setEndDate(0);
        $this->assertTrue($this->subject->hasEnded());
    }

    /**
     * Tests that the count is increased by one.
     *
     * @return void
     */
    public function testCountIsIncreasedByOne()
    {
        $this->subject->setVoteCount('foo', 1);
        $this->subject->increaseVoteCount('foo');
        $this->assertEquals(2, $this->subject->getVoteCount('foo'));
    }

    /**
     * Tests that the total votes are increased by one.
     *
     * @return void
     */
    public function testTotalVotesAreIncreasedByOne()
    {
        $this->subject->setTotalVotes(1);
        $this->subject->increaseTotalVotes();
        $this->assertEquals(2, $this->subject->getTotalVotes());
    }
}
