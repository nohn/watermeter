<?php
use nohn\Watermeter\Reader;
use PHPUnit\Framework\TestCase;

class WatermeterReaderTest extends TestCase
{
    public function testReadout(): void
    {
        $reader = new Reader();
        $this->assertEquals("819.7797", $reader->getReadout());
    }
}
