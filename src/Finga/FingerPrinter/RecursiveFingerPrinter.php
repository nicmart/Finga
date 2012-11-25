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
 * This fingerprinter decorates another one, and fingerprints recursively arrays
 * or Traversable objects
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class RecursiveFingerPrinter implements \Finga\FingerPrinter\FingerPrinterInterface
{
    /**
     * @var FingerPrinterInterface
     */
    private $valueFingerPrinter;

    /**
     * @var FingerPrinterInterface
     */
    private $keyFingerPrinter;

    /**
     * @param FingerPrinterInterface $valueFingerPrinter    The fingerprinter for nodes values
     * @param FingerPrinterInterface $keyFingerPrinter      The fingerprinter for nodes keys
     */
    public function __construct(FingerPrinterInterface $valueFingerPrinter, FingerPrinterInterface $keyFingerPrinter = null)
    {
        $this->valueFingerPrinter = $valueFingerPrinter;

        if (!isset($keyFingerPrinter)) {
            $keyFingerPrinter = new ShaFingerPrinter;
        }

        $this->keyFingerPrinter = $keyFingerPrinter;
    }

    /**
     * A tree is fingerprintable if every node is fingerprintable
     *
     * @param mixed $value  The value to be fingerpinted
     * @return boolean      Is Fingerprintable or not?
     */
    public function isFingerprintable($value)
    {
        if ($this->valueFingerPrinter->isFingerprintable($value)) {
            $fingerPrintable = true;
        } elseif (is_array($value) || $value instanceof \Traversable) {
            $fingerPrintable = true;
            foreach ($value as $key => $subvalue) {
                if (!$this->keyFingerPrinter->isFingerprintable($key)
                    || !$this->isFingerprintable($subvalue))
                {
                    $fingerPrintable = false;
                    break;
                }
            }
        } else {
            $fingerPrintable = false;
        }

        return $fingerPrintable;
    }

    /**
     * @param mixed $value  The value to be fingerpinted
     * @return mixed        The fingerprint
     */
    public function fingerPrint($value)
    {
        $serializedFingerPrints = $this->serializeFingerPrints($value);

        if (!$this->isTree($value)) {
            $serializedFingerPrints = $this->escape($serializedFingerPrints);
        }

        return sha1($serializedFingerPrints);
    }

    private function isTree($value)
    {
        return !$this->valueFingerPrinter->isFingerprintable($value) &&
            (is_array($value) || $value instanceof \Traversable);
    }

    private function serializeFingerPrints($value)
    {
        if ($this->isTree($value)) {
            $pieces = array();

            foreach ($value as $key => $subvalue) {
                $pieces[] = $this->escape($this->keyFingerPrinter->fingerPrint($key));
                $pieces[] = $this->escape($this->serializeFingerPrints($subvalue));
            }

            return implode(':', $pieces);
        }

        return $this->valueFingerPrinter->fingerPrint($value);
    }

    private function escape($string)
    {
        return addcslashes($string, ':\\');
    }
}