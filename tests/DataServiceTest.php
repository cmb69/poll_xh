<?php

namespace Poll;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DataServiceTest extends TestCase
{
    private const CONTENTS = <<<EOS
        Germany\t1
        Brazil\t0
        France\t0
        Argentina\t0
        Spain\t0
        Another team\t0
        %%%MAX%%%\t1
        %%%END%%%\t1528934400
        %%%TOTAL%%%\t1
        EOS;

    public function setUp(): void
    {
        vfsStream::setup("root");
        file_put_contents(vfsStream::url("root/fifa-2018.csv"), self::CONTENTS);
    }

    public function testFindsAllPolls(): void
    {
        $dataService = new DataService(vfsStream::url("root/"));
        $this->assertSame(["fifa-2018"], $dataService->getPollNames());
    }

    public function testFindsPoll(): void
    {
        $dataService = new DataService(vfsStream::url("root/"));
        $this->assertEquals($this->poll(), $dataService->findPoll("fifa-2018"));
    }

    public function testStoresPoll(): void
    {
        unlink(vfsStream::url("root/fifa-2018.csv"));
        $dataService = new DataService(vfsStream::url("root/"));
        $this->assertTrue($dataService->storePoll("fifa-2018", $this->poll()));
        $this->assertStringEqualsFile(vfsStream::url("root/fifa-2018.csv"), self::CONTENTS . "\n");
    }

    private function poll(): Poll
    {
        $poll = new Poll();
        $poll->setName("fifa-2018");
        $poll->setMaxVotes(1);
        $poll->setEndDate(1528934400);
        $poll->setTotalVotes(1);
        $poll->setVoteCount("Germany", 1);
        $poll->setVoteCount("Brazil", 0);
        $poll->setVoteCount("France", 0);
        $poll->setVoteCount("Argentina", 0);
        $poll->setVoteCount("Spain", 0);
        $poll->setVoteCount("Another team", 0);
        return $poll;
    }
}
