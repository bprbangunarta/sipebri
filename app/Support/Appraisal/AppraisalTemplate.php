<?php

namespace App\Support\Appraisal;

/**
 * The form an appraisal of one family of collaterals follows: its sections, the checks that stop an acceptance, and the
 * calculation that turns what the appraiser entered into the figures. A form that changes becomes a new version, so an
 * appraisal keeps the form it was made with.
 *
 * A section has a key, a label, and blocks. A block has a layout (`fields`, `checks`, `ratings`, `grid`, or `photos`) and its
 * items; see `Fields` for how an item is written. The key of an item is unique in the template; the answer of a grid cell is
 * kept under `<section>.<row>.<column>`.
 */
interface AppraisalTemplate
{
    public function key(): string;

    public function version(): int;

    /** The variant of the form this instance follows (a motorcycle or a car), or null for a form that has none. */
    public function variant(): ?string;

    /** A sentence about what the collateral still needs before it can be appraised with this form, or null when nothing is missing. */
    public function missingFacts(): ?string;

    /**
     * @return list<array{key: string, label: string, blocks: list<array<string, mixed>>}>
     */
    public function sections(): array;

    /**
     * Keys of yes/no checks that must be answered "yes" before an appraisal can be accepted.
     *
     * @return list<string>
     */
    public function hardStops(): array;

    /**
     * What the system works out from the answers.
     *
     * @param  array<string, string>  $values  the answers by item key, header columns under `h:<column>`
     */
    public function compute(array $values, AppraisalContext $context): Computation;
}
