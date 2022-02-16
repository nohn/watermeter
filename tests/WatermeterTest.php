<?php
use nohn\Watermeter\Watermeter;
use PHPUnit\Framework\TestCase;

class WatermeterTest extends TestCase
{
    public function testCacheRead(): void
    {
        $watermeter = new Watermeter();
        $this->assertEquals("819.7797", $watermeter->getCachedValue());
    }
}
