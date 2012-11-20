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

use Finga\FingerPrinter\CallableTransformationFingerPrinter;
use Finga\FingerPrinter\FingerPrinterInterface;

/**
 * Unit tests for class CallableTransformationFingerPrinter
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class CallableTransformationFingerPrinterTest extends \PHPUnit_Framework_TestCase
{
    /** @var FingerPrinterInterface */
    protected $fingerPrinterMock;

    /** @var CallableTransformationFingerPrinter */
    protected $fingerPrinter;

    /** @var callable */
    protected $callback;

    public function setUp()
    {
        $this->fingerPrinterMock = $this->getMockBuilder('Finga\FingerPrinter\FingerPrinterInterface')->getMock();

        $this->callback = function($value) { return sha1($value); };

        $this->fingerPrinter = new CallableTransformationFingerPrinter($this->fingerPrinterMock, $this->callback);
    }

    public function testConstructionAndGetTransformation()
    {
        $this->assertEquals($this->callback, $this->fingerPrinter->getTransformation());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorThrowExceptionIfSecondArgumentIsNotACallable()
    {
        new CallableTransformationFingerPrinter($this->fingerPrinterMock, array('1','2'));
    }
}