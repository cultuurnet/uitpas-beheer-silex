<?php

namespace CultuurNet\UiTPASBeheer\Export;

use ValueObjects\StringLiteral\StringLiteral;

abstract class AbstractFileName extends StringLiteral implements FileNameInterface
{
    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->guardDirectoryInfo($value);
        $this->guardExtension($value);
        parent::__construct($value);
    }

    /**
     * @param $fileName
     */
    protected function guardDirectoryInfo($fileName)
    {
        $dirName = pathinfo($fileName, PATHINFO_DIRNAME);

        if (!empty($dirName) && $dirName != ".") {
            throw new \InvalidArgumentException(
                sprintf(
                    'Filename should not contain directory info (%s).',
                    $fileName
                )
            );
        }
    }

    /**
     * @param $fileName
     */
    protected function guardExtension($fileName)
    {
        if (pathinfo($fileName, PATHINFO_EXTENSION) != static::getExtension()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Filename should have %s as extension (%s).',
                    static::getExtension(),
                    $fileName
                )
            );
        }
    }
}
