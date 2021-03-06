<?php

namespace TDDKata\Tests;

use TDDKata\Exception\LoggerException;
use TDDKata\StringCalculator;

class StringCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $calculator;

    public function setup()
    {
        $this->calculator = new StringCalculator();
    }

    public function testAddEmptyStringReturnsZero()
    {
        $result = $this->calculator->add("");
        $this->assertSame(0, $result);
    }

    public function testAddHandlesSingleNumberString()
    {
        $result = $this->calculator->add("2");
        $this->assertSame(2, $result);
    }

    public function testAddHandlesTwoCommaSeparatedNumbersString()
    {
        $result = $this->calculator->add("1,2");
        $this->assertSame(3, $result);
    }

    public function testAddHandlesMultipleCommaSeparatedNumbersString()
    {
        $result = $this->calculator->add("1,2,3");
        $this->assertSame(6, $result);
    }

    public function testAddAllowsNewlineBetweenNumbers()
    {
        $result = $this->calculator->add("1\n2,3");
        $this->assertSame(6, $result);
    }

    public function testAddSupportFirstLineCustomDelimiterDefinition()
    {
        $result = $this->calculator->add("//;1;2");
        $this->assertSame(3, $result);
    }

    public function testAddThrowsExceptionOnNegativeNumbers()
    {
        $this->setExpectedException(
            'TDDKata\Exception\NegativeNumbersNotAllowedException',
            'Negative numbers are not allowed: -1,-2')
        ;

        $this->calculator->add("1,-1,-2");
    }

    public function testAddIgnoreNumbersGreatherThanOneThousand()
    {
        $result = $this->calculator->add("2,1001");
        $this->assertSame(2, $result);
    }

    public function testAddSupportsAnyLengthCustomDelimiters()
    {
        $result = $this->calculator->add("//[***]\n1***2***3");
        $this->assertSame(6, $result);
    }

    public function testAddSupportsMultipleCustomDelimiters()
    {
        $result = $this->calculator->add("//[*][%]\n1*2%3");
        $this->assertSame(6, $result);
    }

    public function testAddSupportsMultipleAnyLengthCustomDelimiters()
    {
        $result = $this->calculator->add("//[**][%]\n1**2%3");
        $this->assertSame(6, $result);
    }

    public function testAddSumIsLogged()
    {
        $loggerMock = $this->getMock('Psr\Log\LoggerInterface');
        $loggerMock->expects($this->once())
                   ->method('info');

        $this->calculator->setLogger($loggerMock);
        $this->calculator->add("1,2");
    }

    public function testAddNotifyWebserviceOnLoggerException()
    {
        $loggerStub = $this->getMock('Psr\Log\LoggerInterface');
        $loggerStub->expects($this->any())
                   ->method('info')
                   ->willThrowException(new LoggerException());

        $webserviceMock = $this->getMock('TDDKata\WebserviceInterface');
        $webserviceMock->expects($this->once())
                       ->method('notify');

        $this->calculator->setLogger($loggerStub);
        $this->calculator->setWebservice($webserviceMock);
        $this->calculator->add("1");
    }
}