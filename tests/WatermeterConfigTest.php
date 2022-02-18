<?php
use nohn\Watermeter\Cache;
use PHPUnit\Framework\TestCase;

class WatermeterCacheTest extends TestCase
{
    public function testCacheRead(): void
    {
        $watermeter = new Cache();
        $this->assertEquals("819.7797", $watermeter->getValue());
    }
}
