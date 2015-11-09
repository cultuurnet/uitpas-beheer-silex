<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

interface StorageInterface
{
    /**
     * @return Text
     */
    public function load();

    /**
     * @param Text $text
     * @return void
     */
    public function save(Text $text);
}
