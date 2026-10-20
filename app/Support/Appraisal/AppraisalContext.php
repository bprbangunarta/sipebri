<?php

namespace App\Support\Appraisal;

/**
 * What a calculation needs to know besides the answers.
 */
final readonly class AppraisalContext
{
    public function __construct(
        public int $ceiling,
        public ?int $certificateLandArea,
    ) {}
}
