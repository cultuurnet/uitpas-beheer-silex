<?php

namespace CultuurNet\UiTPASBeheer\Export\Xls;

use CultuurNet\UiTPASBeheer\Export\AbstractFileName;

final class XlsFileName extends AbstractFileName
{
    /**
     * @return static
     */
    public static function getExtension()
    {
        return 'xls';
    }
}
