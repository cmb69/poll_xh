<?php

require_once '../../plugins/poll/classes/Poll.php';

class PollTest extends PHPUnit_Framework_TestCase
{
    protected $poll;

    public function setUp()
    {
        $this->poll = new Poll_Poll();
        $this->poll->addOption('foo');
        $this->poll->addOption('bar');
        $this->poll->addOption('baz');
    }

    public function testVotingIncreasesVoteCount()
    {
        $before = $this->poll->getVoteCount();
        $this->poll->voteFor('foo');
        $after = $this->poll->getVoteCount();
        $this->assertEquals($before + 1, $after);
    }

    function testVotesAreProperlyCounted()
    {
        $before = $this->poll->votesFor('foo');
        $this->poll->voteFor('foo');
        $after = $this->poll->votesFor('foo');
        $this->assertEquals($before + 1, $after);
    }

    function testRenamingAnOptionKeepsItsVotes()
    {
        $this->poll->voteFor('baz');
        $expected = $this->poll->votesFor('baz');
        $this->poll->renameOption('baz', 'baaz');
        $actual = $this->poll->votesFor('baaz');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException Poll_Exception
     */
    function testVotingForNonExistingOptionThrowsException()
    {
        $this->poll->voteFor('doesnotexist');
    }
}

?>
