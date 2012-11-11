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
use Finga\FingerPrinter\PrefixedFingerPrinter;

/**
 * Unit tests for class PrefixedFingerPrinter
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class PrefixedFingerPrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FingerPrinterInterface
     */
    protected $underlyingFingerprinter;

    /**
     * @var PrefixedFingerPrinter
     */
    protected $prefixedFingerprinter;

    public function setUp()
    {
        $underlyingFingerprinter = $this->getMock('\\Finga\\FingerPrinter\\FingerPrinterInterface');

        $underlyingFingerprinter
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($v){ return is_string($v);}))
        ;

        $underlyingFingerprinter
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnValue('xxx'))
        ;

        $this->underlyingFingerprinter = $underlyingFingerprinter;

        $this->prefixedFingerprinter = new PrefixedFingerPrinter('prefix:', $underlyingFingerprinter);
    }

    public function testIsFingerprintable()
    {
        $this->assertTrue($this->prefixedFingerprinter->isFingerprintable('string:ok'));
        $this->assertFalse($this->prefixedFingerprinter->isFingerprintable(123));
    }

    public function testFingerPrint()
    {
        $this->assertEquals('prefix:xxx', $this->prefixedFingerprinter->fingerPrint('asdasd'));
    }
}