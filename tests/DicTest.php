<?php

namespace Poll;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $plugin_tx;

        $plugin_tx = ["pfw" => []];
    }

    public function testWidgetController(): void
    {
        $this->assertInstanceOf(WidgetController::class, Dic::widgetController());
    }

    public function testInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::infoController());
    }

    public function testMainAdminController(): void
    {
        $this->assertInstanceOf(MainAdminController::class, Dic::mainAdminController());
    }
}
