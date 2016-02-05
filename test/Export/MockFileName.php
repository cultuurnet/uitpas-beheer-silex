<?php

namespace CultuurNet\UiTPASBeheer\Export;

class MockFileName extends AbstractFileName
{
    /**
     * @return string
     */
    public static function getExtension()
    {
        return 'mok';
    }
}
