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
        return new Text(file_get_contents($this->path));
    }

    /**
     * @inheritdoc
     */
    public function save(Text $text)
    {
        file_put_contents($this->path, $text->toNative());
    }
}
