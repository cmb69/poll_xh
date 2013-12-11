<?php

require_once 'vfsStream/vfsStream.php';

require_once '../../plugins/poll/classes/Polls.php';

class PollsTest extends PHPUnit_Framework_TestCase
{
    protected $polls;

    function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));

        file_put_contents(vfsStream::url('test/foo.dat'), '');
        file_put_contents(vfsStream::url('test/bar.dat'), '');
        file_put_contents(vfsStream::url('test/baz.txt'), '');

        $this->polls = new Poll_Polls(vfsStream::url('test'));
    }

    function testFindPollNames()
    {
        $expected = array('foo', 'bar');
        $actual = $this->polls->pollNames();
        $this->assertEquals($expected, $actual);
    }
}

?>
