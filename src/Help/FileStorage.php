<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

class FileStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        $contents = '';
        if (is_readable($this->path)) {
            $contents = file_get_contents($this->path);
        }

        return new Text($contents);
    }

    /**
     * @inheritdoc
     */
    public function save(Text $text)
    {
        file_put_contents($this->path, $text->toNative());
    }
}
