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
 * Composite Fingerprinter
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class CompositeFingerPrinter implements FingerPrinterInterface
{
    /**
     * @var FingerPrinterInterface[]
     */
    private $fingerPrinters;

    /**
     * @param FingerPrinterInterface $fingerPrinter
     * @return CompositeFingerPrinter
     */
    public function add(FingerPrinterInterface $fingerPrinter)
    {
        $this->fingerPrinters[] = $fingerPrinter;

        return $this;
    }

    /**
     * Set children fingerprinters
     *
     * @param array $fingerPrinters
     * @return CompositeFingerPrinter
     */
    public function setFingerPrinters(array $fingerPrinters)
    {
        $this->fingerPrinters = array();

        foreach ($fingerPrinters as $fingerPrinter) {
            $this->add($fingerPrinter);
        }

        return $this;
    }

    /**
     * Get children fingerprinters
     *
     * @return FingerPrinterInterface[]
     */
    public function getFingerPrinters()
    {
        return $this->fingerPrinters;
    }

    /**
     * Is the value fingerprintable?
     *
     * @param mixed $value  The value to be fingerpinted
     * @return boolean      Is Fingerprintable or not?
     */
    public function isFingerprintable($value)
    {
        return (bool) $this->getFingerPrinterForValue($value);
    }

    /**
     * @param mixed $value              The value to be fingerpinted
     * @throws \OutOfBoundsException    Thrown when no fingerprints can handle $value
     * @return mixed                    The fingerprint
     */
    public function fingerPrint($value)
    {
        if ($fingerPrinter = $this->getFingerPrinterForValue($value)) {
            return $fingerPrinter->fingerPrint($value);
        }

        throw new \OutOfBoundsException('None of children fingerprinters can fingerprint the provided value');
    }

    /**
     * Retrieve a fingerprinter that can fingerprint $value
     * @param $value                        The value to fingerprint
     * @return bool|FingerPrinterInterface  The fingerprinter that can handle the fingerprint
     */
    private function getFingerPrinterForValue($value)
    {
        foreach ($this->fingerPrinters as $fingerPrinter) {
            if ($fingerPrinter->isFingerprintable($value))
                return $fingerPrinter;
        }

        return false;
    }
}