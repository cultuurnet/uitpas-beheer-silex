<?php

namespace CultuurNet\UiTPASBeheer\Http;

use ValueObjects\StringLiteral\StringLiteral;

final class ContentDispositionHeader extends StringLiteral
{
    /**
     * @param string $fileName
     * @return self
     */
    public static function fromFileName($fileName)
    {
        return new self('attachment; filename="' . $fileName . '"');
    }
}
