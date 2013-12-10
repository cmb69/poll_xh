<?php

/**
 * Class modelling a poll.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Poll
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Poll_XH
 */

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
     * @var string
     */
    var $filename;

    /**
     * @var string
     */
    var $name;

    /**
     * @var int
     */
    var $endingTime;

    /**
     * @var array
     */
    var $options;

    /**
     * @var int
     */
    var $voteCount;

    /**
     * Initialized a new instance.
     *
     * @param string $filename Path of the data file.
     */
    function Poll_Poll($filename)
    {
        $this->filename = $filename;
        $this->name = basename($filename, '.dat');

        $this->options = array();
        $this->votes = 0;
    }

    function save()
    {
        file_put_contents($this->filename, serialize($this));
    }

    function getEndingTime()
    {
        return $this->endingTime;
    }

    function setEndingTime($endingTime)
    {
        $this->endingTime = $endingTime;
    }

    function getVoteCount()
    {
        return $this->voteCount;
    }

    function addOption($option)
    {
        $this->options[$option] = 0;
    }

    function renameOption($oldName, $newName)
    {
        $votes = $this->options[$oldName];
        unset($this->options[$oldName]);
        $this->options[$newName] = $votes;
    }

    function voteFor($option)
    {
        $this->options[$option]++;
        $this->voteCount++;
    }

    function votesFor($option)
    {
        return $this->options[$option];
    }
}

?>
