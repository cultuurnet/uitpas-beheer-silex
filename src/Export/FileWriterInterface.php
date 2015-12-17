<?php

namespace CultuurNet\UiTPASBeheer\Export;

interface FileWriterInterface
{
    public function getHttpHeaders();

    public function open();

    public function write();

    public function close();
}
