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

use Finga\FingerPrinter\ShaFingerPrinter;

/**
 * Unit tests for class ShaFingerPrinter
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class ShaFingerPrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShaFingerPrinter
     */
    protected $fingerPrinter;

    public function setUp()
    {
        $this->fingerPrinter = new ShaFingerPrinter;
    }

    public function testIsFingerprintable()
    {
        $this->assertFalse($this->fingerPrinter->isFingerprintable(12));
        $this->assertFalse($this->fingerPrinter->isFingerprintable(new \StdClass));

        $this->assertTrue($this->fingerPrinter->isFingerprintable(''));
        $this->assertTrue($this->fingerPrinter->isFingerprintable('asdasdasdasd asd asd'));
    }

    public function testFingerprint()
    {
        $this->assertEquals(sha1('ciao ciao'), $this->fingerPrinter->fingerPrint('ciao ciao'));
        $this->assertEquals(sha1(''), $this->fingerPrinter->fingerPrint(''));

        $this->setExpectedException('OutOfBoundsException');

        $this->fingerPrinter->fingerPrint(123);
    }
}