<?php

namespace Poll;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;
use Poll\Model\DataService;

class InfoControllerTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $dataServiceStub = $this->createStub(DataService::class);
        $dataServiceStub->method("getFolder")->willReturn("./content/poll/");
        $sut = new InfoController(
            "./plugins/poll/",
            $dataServiceStub,
            new FakeSystemChecker(),
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["poll"])
        );
        Approvals::verifyHtml($sut->defaultAction());
    }
}
