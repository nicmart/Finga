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
 * Interface for FingerPrinters
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
interface FingerPrinterInterface
{
    /**
     * @param mixed $value  The value to be fingerpinted
     * @return boolean      Is Fingerprintable or not?
     */
    public function isFingerprintable($value);

    /**
     * @param mixed $value  The value to be fingerpinted
     * @return mixed        The fingerprint
     */
    public function fingerPrint($value);
}