<?php

namespace Poll;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class MainAdminControllerTest extends TestCase
{
    public function testRendersPollResults(): void
    {
        $dataService = $this->createStub(DataService::class);
        $dataService->method("getPollNames")->willreturn(["foo"]);
        $dataService->method("findPoll")->willReturn($this->poll());
        $sut = new MainAdminController(
            $dataService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["poll"])
        );
        Approvals::verifyHtml($sut->defaultAction());
    }

    private function poll(): Poll
    {
        $poll = new Poll();
        $poll->setEndDate(1147483647);
        $poll->setVoteCount("Germany", 1);
        $poll->setVoteCount("Brazil", 0);
        $poll->setTotalVotes(1);
        return $poll;
    }
}