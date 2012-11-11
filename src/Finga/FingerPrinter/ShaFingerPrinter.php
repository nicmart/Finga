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
 * Sha1-based fingerprinter for strings
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class ShaFingerPrinter implements FingerPrinterInterface
{
    /**
     * @param mixed $value  The value to be fingerpinted
     * @return boolean      Is Fingerprintable or not?
     */
    public function isFingerprintable($value)
    {
        return is_string($value);
    }

    /**
     * @param mixed $value  The value to be fingerpinted
     * @throws \OutOfBoundsException
     * @return mixed        The fingerprint
     */
    public function fingerPrint($value)
    {
        if (!$this->isFingerprintable($value)) {
            throw new \OutOfBoundsException('The passed value is not a string');
        }
        return sha1($value);
    }
}