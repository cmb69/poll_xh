<?php

/**
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
 * @category CMSimple_XH
 * @package  Poll
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Poll_XH
 */
class Poll_Polls
{
    /**
     * @var string
     */
    var $folder;

    /**
     * Initializes a new Instance.
     *
     * @param string $folder Path of a data folder.
     */
    function Poll_Polls($folder)
    {
        $this->folder = $folder;
    }

    function filename($pollName)
    {
        return $this->folder . '/' . $pollName . '.dat';
    }

    function pollNames()
    {
        $files = array();
        $handle = opendir($this->folder);
        while (($entry = readdir($handle)) !== false) {
            if (is_file($this->folder . '/' . $entry)
                && pathinfo($entry, PATHINFO_EXTENSION) == 'dat'
            ) {
                $files[] = substr($entry, 0, -4);
            }
        }
        return $files;
    }

    function poll($name)
    {
        $contents = file_get_contents($this->filename($name));
        $poll = unserialize($contents);
        return $poll;
    }
}

?>

