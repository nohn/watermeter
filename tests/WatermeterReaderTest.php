<?php


use nohn\Watermeter\Reader;
use PHPUnit\Framework\TestCase;

class WatermeterReaderTest extends TestCase
{
    public function testAnalogGaugesRead(): void
    {
        $reader = new Reader();
        $this->assertEquals("7797", $reader->readAnalogGauges());
    }
}
