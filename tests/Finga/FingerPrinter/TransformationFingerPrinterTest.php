<?php
/*
 * This file is part of Finga.
 *
 * (c) 2012 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Finga\Test\FingerPrinter;

use Finga\FingerPrinter\TransformationFingerPrinter;
use Finga\FingerPrinter\FingerPrinterInterface;

/**
 * Unit tests for class TransformationFingerPrinterTest
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class TransformationFingerPrinterTest extends \PHPUnit_Framework_TestCase
{
    /** @var FingerPrinterInterface */
    protected $fingerPrinterMock;

    /** @var TransformationFingerPrinter */
    protected $trFingerPrinterMock;

    public function setUp()
    {
        $this->fingerPrinterMock = $this->getMockBuilder('Finga\FingerPrinter\FingerPrinterInterface')->getMock();

        //This mock fingerprinter accepts only strings and prefix them with a "xxx:" string.
        $this->fingerPrinterMock
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value){ return is_string($value); }))
        ;

        $this->fingerPrinterMock
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnCallback(function($value){ return 'xxx:' . $value; }))
        ;

        //This mock for the transformationFingerPrinter class transform each value to the string
        //"transform"
        $this->trFingerPrinterMock = $this->getMockBuilder('Finga\FingerPrinter\TransformationFingerPrinter')
            ->setConstructorArgs(array($this->fingerPrinterMock))
            ->setMethods(array('getTransformation'))
            ->getMock()
        ;

        $this->trFingerPrinterMock
            ->expects($this->any())
            ->method('getTransformation')
            ->will($this->returnValue(function(){ return 'transformed'; }))
        ;
    }

    public function testIsFingerprintableCheckIfTheTransformedValueIsFingerprintable()
    {
        $this->assertTrue($this->trFingerPrinterMock->isFingerprintable(12));
        $this->assertTrue($this->trFingerPrinterMock->isFingerPrintable(array()));
        $this->assertTrue($this->trFingerPrinterMock->isFingerPrintable('asdasdasd'));
    }

    public function testFingerPrintFingerprintsTheTransformedValue()
    {
        $this->assertEquals('xxx:transformed', $this->trFingerPrinterMock->fingerPrint('asdasdasdasd'));
        $this->assertEquals('xxx:transformed', $this->trFingerPrinterMock->fingerPrint(12));
    }
}