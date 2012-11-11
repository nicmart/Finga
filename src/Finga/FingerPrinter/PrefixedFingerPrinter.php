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
 * This class implements the Decorator design pattern, and simply put a prefix
 * in front of underlying fingerprinter responses
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class PrefixedFingerPrinter implements FingerPrinterInterface
{
    /**
     * @var FingerPrinterInterface
     */
    private $fingerPrinter;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     * @param FingerPrinterInterface $fingerPrinter
     */
    public function __construct($prefix, FingerPrinterInterface $fingerPrinter)
    {
        $this->prefix = $prefix;
        $this->fingerPrinter = $fingerPrinter;
    }

    /**
     * @param mixed $value  The value to be fingerpinted
     * @return boolean      Is Fingerprintable or not?
     */
    public function isFingerprintable($value)
    {
        return $this->fingerPrinter->isFingerprintable($value);
    }

    /**
     * @param mixed $value  The value to be fingerpinted
     * @return mixed        The fingerprint
     */
    public function fingerPrint($value)
    {
        return $this->prefix . (string) $this->fingerPrinter->fingerPrint($value);
    }

}