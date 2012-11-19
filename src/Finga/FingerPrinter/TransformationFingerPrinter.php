<?php
/*
 * This file is part of Finga.
 *
 * (c) 2012 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Finga\FingerPrinter;

/**
 * This Decorator class picks an arbitrary implementation of FingerPrinterInterface and, before to pass a value
 * to that implementation, transforms the value with the callback returned by
 * @see TransformationFingerPrinter::getTransformation
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
abstract class TransformationFingerPrinter implements \Finga\FingerPrinter\FingerPrinterInterface
{
    /**
     * @var FingerPrinterInterface
     */
    private $fingerPrinter;

    /**
     * Returns a callback to be called before an object is fingerprinted
     *
     * @return callable
     */
    abstract public function getTransformation();

    public function __construct(FingerPrinterInterface $fingerPrinter)
    {
        $this->fingerPrinter = $fingerPrinter;
    }

    /**
     * @param mixed $value  The value to be fingerpinted
     * @return boolean      Is Fingerprintable or not?
     */
    public function isFingerprintable($value)
    {
        $callback = $this->getTransformation();

        return $this->fingerPrinter->isFingerprintable($callback($value));
    }

    /**
     * @param mixed $value  The value to be fingerpinted
     * @return mixed        The fingerprint
     */
    public function fingerPrint($value)
    {
        $callback = $this->getTransformation();

        return $this->fingerPrinter->fingerPrint($callback($value));
    }

}