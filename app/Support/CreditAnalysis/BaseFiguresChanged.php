<?php

namespace App\Support\CreditAnalysis;

use RuntimeException;

/** Thrown inside the transaction of a correction to undo a change that touches the figures the committee decided on. */
final class BaseFiguresChanged extends RuntimeException
{
    /**
     * @param  list<string>  $figures
     */
    public function __construct(public readonly array $figures)
    {
        parent::__construct('The approved figures changed.');
    }
}
