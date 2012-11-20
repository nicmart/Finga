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
 * This is a TrasformationFingerPrinter whose callback is given at runtine
 * passing it to the constructor
 *
 * @package    Finga
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
class CallableTransformationFingerPrinter extends TransformationFingerPrinter
{
    private $callback;

    /**
     * @param FingerPrinterInterface    $fingerPrinter The underlying fingerprinter
     * @param callable                  $callback      The callback responsible of the pre-fingerprinting transformation
     * @throws \InvalidArgumentException
     */
    public function __construct(FingerPrinterInterface $fingerPrinter, $callback)
    {
        if (!is_callable($callback))
            throw new \InvalidArgumentException("The second argument must be a valid PHP callable");

        $this->callback = $callback;
        parent::__construct($fingerPrinter);
    }

    /**
     * Returns a callback to be called before an object is fingerprinted
     *
     * @return callable
     */
    public function getTransformation()
    {
        return $this->callback;
    }
}