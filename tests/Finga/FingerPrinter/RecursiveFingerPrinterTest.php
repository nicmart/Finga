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
use  Finga\FingerPrinter\RecursiveFingerPrinter;

/**
 * Unit tests for class RecursiveFingerPrinterTest
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class RecursiveFingerPrinterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  RecursiveFingerPrinter */
    protected $fingerPrinter;

    /** @var  FingerPrinterInterface */
    protected $valueFingerPrinter;

    /** @var  FingerPrinterInterface */
    protected $keyFingerPrinter;

    public function setUp()
    {
        //This mock fingerprints strings that do not start with a "x" prefixing them with a "notx:" string
        $this->valueFingerPrinter = $this->getMock('Finga\\FingerPrinter\\FingerPrinterInterface');

        $this->valueFingerPrinter
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value) { return is_string($value) && $value[0] != 'x'; }))
        ;

        $this->valueFingerPrinter
            ->expects($this->any())
            ->method('fingerPrint')
            ->will($this->returnCallback(function($value) { return 'notx:' . $value; }))
        ;

        $this->keyFingerPrinter = $this->valueFingerPrinter;

        $this->fingerPrinter = new RecursiveFingerPrinter($this->valueFingerPrinter, $this->keyFingerPrinter);
    }

    public function testIsFingerprintableForLeafNodes()
    {
        $this->assertTrue($this->fingerPrinter->isFingerprintable('12345'));
        $this->assertFalse($this->fingerPrinter->isFingerprintable(12345));
        $this->assertFalse($this->fingerPrinter->isFingerprintable('xstring'));
    }

    public function testANodeIsFingerPrintableIfEachSubnodesAreFingerprintableWithTheirKeys()
    {
        $simpleAry = array('a' => 'v', 'b' => 'c');
        $goodAry = array('a' => 'asdads', 'c' => 'asd', 'd' => array('a' => 'b', 'c' => 'd'));
        $aryWithBadIndex = array('a' => 'asdads', 'c' => 'asd', 'd' => array('xa' => 'b', 'c' => 'd'));
        $aryWithBadValue = array('a' => 'asdads', 'c' => 'asd', 'd' => array('a' => 'xb', 'c' => 'd'));

        $this->assertTrue($this->fingerPrinter->isFingerprintable($simpleAry));
        $this->assertTrue($this->fingerPrinter->isFingerprintable($goodAry));
        $this->assertFalse($this->fingerPrinter->isFingerprintable($aryWithBadIndex));
        $this->assertFalse($this->fingerPrinter->isFingerprintable($aryWithBadValue));
    }

    public function testIsFingerPrintableConsiderFingerprintableSubnodesAsLeaves()
    {
        $fpMock = $this->getMock('Finga\\FingerPrinter\\FingerPrinterInterface');

        //This mock consider fingerprintable string values and arrays with one value.
        $fpMock
            ->expects($this->any())
            ->method('isFingerprintable')
            ->will($this->returnCallback(function($value)
                {
                    return is_string($value) || (is_array($value) && count($value) == 1);
                }
            ))
        ;

        $fingerPrinter = new RecursiveFingerPrinter($fpMock);

        //True because array(123) is considered as a leaf, while 123 would not be fingerprintable
        $this->assertTrue($fingerPrinter->isFingerprintable(array('a' => 123)));

        //False because this time the array is considered as a tree, and so its subvalues
        //are analyzed
        $this->assertFalse($fingerPrinter->isFingerprintable(array('a' => 123, 'b' => 'ciao')));
    }

    public function testFingerPrintLeafValuesEscapesColumnsAndBackslashes()
    {
        $value = 'asdasd:asdasd\asasd';
        $escapedFp = addcslashes($this->valueFingerPrinter->fingerPrint($value), '\\:');

        $this->assertEquals(sha1($escapedFp), $this->fingerPrinter->fingerPrint($value));
    }

    public function testFingerPrintSimpleTreeValues()
    {
        $value = array('a' => '\\v', 'b' => 'c');

        $expected = sha1(implode(':', array(
            'notx\\:a', 'notx\\:\\\\v', 'notx\\:b', 'notx\\:c')
        ));

        $this->assertEquals($expected, $this->fingerPrinter->fingerPrint($value));
    }

    public function testFingerPrintNestedTreeValues()
    {
        $value = array('a' => '\\v', 'b' => array('y' => 'v', 'z' => 'c'));

        $subvalueFp = implode(':', array(
            'notx\\:y', 'notx\\:v', 'notx\\:z', 'notx\\:c'
        ));

        $expected = sha1(implode(':', array(
            'notx\\:a', 'notx\\:\\\\v', 'notx\\:b', addcslashes($subvalueFp, ':\\')
        )));

        $this->assertEquals($expected, $this->fingerPrinter->fingerPrint($value));
    }

    public function testFingerPrintIsSortingSensible()
    {
        $ary1 = array ('a' => 'b', 'c' => 'd');
        $ary2 = array ('c' => 'd', 'a' => 'b');

        $this->assertNotEquals(
            $this->fingerPrinter->fingerPrint($ary1),
            $this->fingerPrinter->fingerPrint($ary2)
        );
    }
}