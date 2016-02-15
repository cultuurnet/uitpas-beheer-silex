<?php

namespace CultuurNet\UiTPASBeheer\Export;

class AbstractFileNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_guards_against_filenames_with_directory_info()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new MockFileName('/directory/info/filename.mok');
    }

    /**
     * @test
     */
    public function it_guards_against_filenames_with_incorrect_extension()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new MockFileName('filename.txt');
    }
}
