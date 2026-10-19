<?php

namespace App\Support\ReferenceImport;

use RuntimeException;

/**
 * An import that saved nothing: the message is a sentence for the person, the issues are their rows.
 */
class ImportRefused extends RuntimeException
{
    /**
     * @param  list<string>  $issues
     */
    public function __construct(string $message, public readonly array $issues = [])
    {
        parent::__construct($message);
    }
}
