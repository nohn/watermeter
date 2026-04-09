<?php

use nohn\Watermeter\Reader;
use PHPUnit\Framework\TestCase;

class WatermeterDebugOutputTest extends TestCase
{
    /**
     * @covers \nohn\Watermeter\Reader::getReadout
     * @covers \nohn\Watermeter\Reader::readDigits
     * @covers \nohn\Watermeter\Reader::readGauges
     */
    public function testNoDebugOutputWhenDisabled(): void
    {
        // When debug is false (default), there should be no output from getReadout()
        $reader = new Reader(false);
        
        ob_start();
        $reader->getReadout();
        $output = ob_get_clean();
        
        $this->assertEmpty($output, "Expected no debug output when debug is disabled, but got: " . $output);
    }

    /**
     * @covers \nohn\Watermeter\Reader::getReadout
     * @covers \nohn\Watermeter\Reader::readDigits
     * @covers \nohn\Watermeter\Reader::readGauges
     * @covers \nohn\Watermeter\Reader::debugGauge
     */
    public function testDebugOutputWhenEnabled(): void
    {
        // When debug is true, there should be some output from getReadout()
        $reader = new Reader(true);
        
        ob_start();
        $reader->getReadout();
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output, "Expected debug output when debug is enabled, but got nothing.");
    }
}
