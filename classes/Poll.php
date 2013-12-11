<?php

/**
 * Class modelling a poll.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
 */

class Poll_Exception extends Exception {}


/**
 * Class modelling a poll.
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
     * @var int
     */
    protected $endingTime;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var int
     */
    protected $voteCount;

    /**
     * Initializes a new instance.
     */
    public function __construct()
    {
        $this->endingTime = time();
        $this->options = array();
        $this->voteCount = 0;
    }

    public function getEndingTime()
    {
        return $this->endingTime;
    }

    public function setEndingTime($endingTime)
    {
        $this->endingTime = $endingTime;
    }

    public function getOptionNames()
    {
        return array_keys($this->options);
    }

    public function addOption($option)
    {
        $this->options[$option] = 0;
    }

    public function renameOption($oldName, $newName)
    {
        $votes = $this->options[$oldName];
        unset($this->options[$oldName]);
        $this->options[$newName] = $votes;
    }

    public function getVoteCount()
    {
        return $this->voteCount;
    }

    public function voteFor($option)
    {
        if (!isset($this->options[$option])) {
            throw new Poll_Exception();
        }
        $this->options[$option]++;
        $this->voteCount++;
    }

    public function votesFor($option)
    {
        return $this->options[$option];
    }
}

?>
