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

use Finga\FingerPrinter\FingerPrinterInterface;
use Finga\FingerPrinter\CompositeFingerPrinter;

/**
 * Unit tests for class CompositeFingerPrinter
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class CompositeFingerPrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FingerPrinterInterface[]
     */
    protected $fingerPrinters;

    /**
     * @var CompositeFingerPrinter
     */
    protected $compositeFP;

    public function setUp()
    {
        $this->compositeFP = new CompositeFingerPrinter;

        $f0 = $this->getMock('\\Finga\\FingerPrinter\\FingerPrinterInterface');
        $f1 = $this->getMock('\\Finga\\FingerPrinter\\FingerPrinterInterface');
        $f2 = $this->getMock('\\Finga\\FingerPrinter\\FingerPrinterInterface');
        $f3 = $this->getMock('\\Finga\\FingerPrinter\\FingerPrinterInterface');

        $f0
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value){ return is_string($value);}))
        ;
        $f0
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnValue('f0'));
        ;

        $f1
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value){ return is_integer($value);}))
        ;
        $f1
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnValue('f1'));
        ;

        $f2
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value){ return is_object($value);}))
        ;
        $f2
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnValue('f2'));
        ;

        $f3
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value){ return is_string($value);}))
        ;
        $f3
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnValue('f3'));
        ;

        $this->fingerPrinters = array($f0, $f1, $f2, $f3);
    }

    public function testGetAndSetFingerprinters()
    {
        $this->compositeFP->setFingerPrinters($this->fingerPrinters);

        $this->assertEquals($this->fingerPrinters, $this->compositeFP->getFingerPrinters());
    }

    public function testAdd()
    {
        $this->compositeFP
            ->add($this->fingerPrinters[0])
            ->add($this->fingerPrinters[1])
        ;

        $this->assertEquals(array($this->fingerPrinters[0], $this->fingerPrinters[1]), $this->compositeFP->getFingerPrinters());
    }

    public function testFingerprint()
    {
        $this->compositeFP->setFingerPrinters($this->fingerPrinters);

        $this->assertEquals('f0', $this->compositeFP->fingerPrint('string'));
        $this->assertEquals('f1', $this->compositeFP->fingerPrint(1234));
        $this->assertEquals('f2', $this->compositeFP->fingerPrint(new \StdClass));

        $this->setExpectedException('OutOfBoundsException');
        $this->compositeFP->fingerPrint(12.2);
    }

    public function testFirstChildrenCapableOfFingerprintingWins()
    {
        $this->compositeFP
            ->add($this->fingerPrinters[0])
            ->add($this->fingerPrinters[3])
        ;

        $this->assertEquals('f0', $this->compositeFP->fingerPrint('string'));
    }

    public function testIsFingerprintable()
    {
        $this->compositeFP->setFingerPrinters($this->fingerPrinters);

        $this->assertTrue($this->compositeFP->isFingerprintable('string'));
        $this->assertTrue($this->compositeFP->isFingerprintable(1234));
        $this->assertTrue($this->compositeFP->isFingerprintable(new \StdClass));
        $this->assertFalse($this->compositeFP->isFingerprintable(12.23));
    }
}