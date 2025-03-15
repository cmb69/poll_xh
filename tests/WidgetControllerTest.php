<?php

namespace Poll;

use ApprovalTests\Approvals;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\View;

class WidgetControllerTest extends TestCase
{
    public function testReportsInvalidName(): void
    {
        $dataService = $this->createStub(DataService::class);
        $sut = new WidgetController(
            $dataService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["poll"])
        );
        $this->assertStringContainsString(
            "Invalid poll name 'invalid name'! (must consist of 'a'-'z', '0'-'9' and '-' only)",
            $sut("invalid name")
        );
    }

    public function testRenderVotingForm(): void
    {
        $_SERVER["REMOTE_ADDR"] = "79.251.201.250";
        vfsStream::setup("root");
        $dataService = $this->createStub(DataService::class);
        $dataService->method("getFolder")->willReturn(vfsStream::url("root/"));
        $dataService->method("findPoll")->willReturn($this->poll());
        $sut = new WidgetController(
            $dataService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["poll"])
        );
        Approvals::verifyHtml($sut("fifa-2018"));
    }

    private function poll(): Poll
    {
        $poll = new Poll();
        $poll->setEndDate(2147483647);
        $poll->setMaxVotes(1);
        $poll->setVoteCount("Germany", 1);
        $poll->setVoteCount("Brazil", 0);
        $poll->setTotalVotes(1);
        return $poll;
    }
}
