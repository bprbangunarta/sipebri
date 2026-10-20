<?php

namespace App\Support\Appraisal;

/**
 * The result of a calculation: the figures that are kept on the appraisal and the cells that are shown beside the answers.
 */
final readonly class Computation
{
    /**
     * @param  array<string, int|float|string|null>  $cells  by `<section>.<row>.<column>` or `<section>.<item>`
     */
    public function __construct(
        public int $marketValue,
        public int $liquidationValue,
        public array $cells,
    ) {}
}
