<?php

/**
 * Testing the polls.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
 */

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
class PollTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test subject.
     *
     * @var Poll_Poll
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->subject = new Poll_Poll();
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

?>
