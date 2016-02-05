<?php

namespace CultuurNet\UiTPASBeheer\Export;

interface FileNameInterface
{
    /**
     * Returns a hardcoded filename extension for the specific implementation.
     *
     * @return string
     */
    public static function getExtension();
}
