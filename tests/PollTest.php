<?php

namespace Poll;

use PHPUnit\Framework\TestCase;

class PollTest extends TestCase
{
    /**
     * @var Poll
     */
    protected $subject;

    public function setUp(): void
    {
        $this->subject = new Poll();
    }

    /**
     * @return void
     */
    public function testHasEnded()
    {
        $this->subject->setEndDate(0);
        $this->assertTrue($this->subject->hasEnded());
    }

    /**
     * @return void
     */
    public function testCountIsIncreasedByOne()
    {
        $this->subject->setVoteCount('foo', 1);
        $this->subject->increaseVoteCount('foo');
        $this->assertEquals(2, $this->subject->getVoteCount('foo'));
    }

    /**
     * @return void
     */
    public function testTotalVotesAreIncreasedByOne()
    {
        $this->subject->setTotalVotes(1);
        $this->subject->increaseTotalVotes();
        $this->assertEquals(2, $this->subject->getTotalVotes());
    }
}
