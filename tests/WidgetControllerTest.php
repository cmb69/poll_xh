<?php

namespace Poll;

use ApprovalTests\Approvals;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use Poll\Model\DataService;
use Poll\Model\Poll;

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
            $sut(new FakeRequest(), "invalid name")->output()
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
        Approvals::verifyHtml($sut(new FakeRequest(), "fifa-2018")->output());
    }

    public function testRendersResultsAfterSuccessfulVoting(): void
    {
        $_SERVER["REMOTE_ADDR"] = "79.251.201.250";
        $_POST = ["poll_fifa-2018" => ["Germany"]];
        vfsStream::setup("root");
        $dataService = $this->createStub(DataService::class);
        $dataService->method("getFolder")->willReturn(vfsStream::url("root/"));
        $dataService->method("findPoll")->willReturn($this->poll());
        $dataService->method("storePoll")->willReturn(true);
        $dataService->method("registerVote")->willReturn(true);
        $sut = new WidgetController(
            $dataService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["poll"])
        );
        Approvals::verifyHtml($sut(new FakeRequest(), "fifa-2018")->output());
    }

    private function poll(): Poll
    {
        $poll = new Poll();
        $poll->setName("fifa-2018");
        $poll->setEndDate(2147483647);
        $poll->setMaxVotes(1);
        $poll->setVoteCount("Germany", 1);
        $poll->setVoteCount("Brazil", 0);
        $poll->setTotalVotes(1);
        return $poll;
    }
}
