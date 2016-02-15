<?php

namespace CultuurNet\UiTPASBeheer\Export;

interface FileWriterInterface
{
    /**
     * @return string[]
     */
    public function getHttpHeaders();

    /**
     * @return string
     */
    public function open();

    /**
     * @param array $data
     *   Data to write to the file. Each array value represents a single
     *   column value.
     *
     * @return string
     */
    public function write(array $data);

    /**
     * @return string
     */
    public function close();
}
