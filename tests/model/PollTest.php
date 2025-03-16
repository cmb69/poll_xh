<?php

namespace Poll\Model;

use PHPUnit\Framework\TestCase;

class PollTest extends TestCase
{
    /** @var Poll */
    protected $subject;

    public function setUp(): void
    {
        $this->subject = new Poll();
    }

    public function testHasEnded(): void
    {
        $this->subject->setEndDate(0);
        $this->assertTrue($this->subject->hasEnded());
    }

    public function testCountIsIncreasedByOne(): void
    {
        $this->subject->setVoteCount('foo', 1);
        $this->subject->increaseVoteCount('foo');
        $this->assertEquals(2, $this->subject->getVoteCount('foo'));
    }

    public function testTotalVotesAreIncreasedByOne(): void
    {
        $this->subject->setTotalVotes(1);
        $this->subject->increaseTotalVotes();
        $this->assertEquals(2, $this->subject->getTotalVotes());
    }
}
