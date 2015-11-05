<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;

class FileStorageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('var'));
    }

    /**
     * @test
     */
    public function it_can_load_text_from_a_file()
    {
        vfsStreamWrapper::getRoot()->addChild(
            (new vfsStreamFile('help.md'))->withContent('Some *markdown*')
        );

        $filePath = vfsStream::url('var/help.md');
        $storage = new FileStorage($filePath);

        $actualText = $storage->load();

        $this->assertEquals(
            new Text('Some *markdown*'),
            $actualText
        );
    }

    /**
     * @test
     */
    public function it_loads_empty_text_if_file_is_missing()
    {
        $filePath = vfsStream::url('var/help.md');
        $storage = new FileStorage($filePath);

        $actualText = $storage->load();

        $this->assertEquals(
            new Text(''),
            $actualText
        );
    }

    /**
     * @test
     */
    public function it_can_save_text_to_a_file()
    {
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('help.md'));

        $filePath = vfsStream::url('var/help.md');
        $storage = new FileStorage($filePath);

        $storage->save(new Text('Some *markdown*'));

        /** @var vfsStreamFile $file */
        $file = vfsStreamWrapper::getRoot()->getChild('help.md');

        $this->assertInstanceOf(vfsStreamFile::class, $file);
        $this->assertEquals('Some *markdown*', $file->getContent());
    }
}
