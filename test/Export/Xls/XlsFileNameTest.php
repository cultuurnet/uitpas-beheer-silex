<?php

namespace CultuurNet\UiTPASBeheer\Export\Xls;

class XlsFileNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_the_correct_extension()
    {
        $this->assertEquals('xls', XlsFileName::getExtension());
    }
}
