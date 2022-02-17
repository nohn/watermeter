<?php


use nohn\Watermeter\Reader;
use PHPUnit\Framework\TestCase;

class WatermeterReaderTest extends TestCase
{
    public function testReadDigits(): void
    {
        $reader = new Reader();
        $this->assertEquals("819", $reader->readDigits());
    }

    public function testReadGauges(): void
    {
        $reader = new Reader();
        $this->assertEquals("7797", $reader->readGauges());
    }

    public function testRead(): void
    {
        $reader = new Reader();
        $this->assertEquals("819.7797", $reader->read());
    }
}
