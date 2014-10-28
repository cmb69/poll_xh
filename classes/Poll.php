<?php

/**
 * The polls.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
 */

/**
 * The polls.
 *
 * @category CMSimple_XH
 * @package  Poll
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Poll_XH
 */
class Poll_Poll
{
    /**
     * The name.
     *
     * @var string
     */
    protected $name;

    /**
     * The maximum number of votes.
     *
     * @var int
     */
    protected $maxVotes;

    /**
     * The end date of the voting.
     *
     * @var int A UNIX timestamp.
     */
    protected $endDate;

    /**
     * The total number of votes so far.
     *
     * @var int
     */
    protected $totalVotes;

    /**
     * The votes as map from option name to number of votes.
     *
     * @var array<string, int>
     */
    protected $votes;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the maximum number of votes.
     *
     * @return int A UNIX timestamp.
     */
    public function getMaxVotes()
    {
        return $this->maxVotes;
    }

    /**
     * Returns the end date of the voting.
     *
     * @return int
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Returns whether the poll has ended.
     *
     * @return bool
     */
    public function hasEnded()
    {
        return $this->getEndDate() <= time();
    }

    /**
     * Returns the total number of votes so far.
     *
     * @return int
     */
    public function getTotalVotes()
    {
        return $this->totalVotes;
    }

    /**
     * Returns the vote count of an option.
     *
     * @param string $optionName An option name.
     *
     * @return int
     */
    public function getVoteCount($optionName)
    {
        return $this->votes[$optionName];
    }

    /**
     * Returns the votes as map from option name to number of votes.
     *
     * @return array<string, int>
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Sets the name.
     *
     * @param string $name A name.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the maximum number of votes.
     *
     * @param int $number A number.
     *
     * @return void
     */
    public function setMaxVotes($number)
    {
        $this->maxVotes = $number;
    }

    /**
     * Returns the end date of the voting.
     *
     * @param int $timestamp A UNIX timestamp.
     *
     * @return void
     */
    public function setEndDate($timestamp)
    {
        $this->endDate = $timestamp;
    }

    /**
     * Sets the total number of votes so far.
     *
     * @param int $number A number.
     *
     * @return void
     */
    public function setTotalVotes($number)
    {
        $this->totalVotes = $number;
    }

    /**
     * Increases the total number of votes by one.
     *
     * @return void
     */
    public function increaseTotalVotes()
    {
        $this->totalVotes++;
    }

    /**
     * Sets the vote count for an option.
     *
     * @param string $optionName An option name.
     * @param int    $count      A vote count.
     *
     * @return void
     */
    public function setVoteCount($optionName, $count)
    {
        $this->votes[$optionName] = $count;
    }

    /**
     * Increases the vote count of an option by one.
     *
     * @param string $optionName An option name.
     *
     * @return void
     */
    public function increaseVoteCount($optionName)
    {
        $this->votes[$optionName]++;
    }

    /**
     * Sorts the votes descending by count.
     *
     * @return void
     */
    public function sortVotes()
    {
        arsort($this->votes);
    }
}

?>
