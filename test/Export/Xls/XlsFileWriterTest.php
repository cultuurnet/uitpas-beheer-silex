<?php

namespace CultuurNet\UiTPASBeheer\Export\Xls;

use ValueObjects\StringLiteral\StringLiteral;

class XlsFileWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XlsFileName
     */
    private $fileName;

    /**
     * @var StringLiteral
     */
    private $encoding;

    /**
     * @var StringLiteral
     */
    private $worksheetTitle;

    /**
     * @var XlsFileWriter
     */
    private $writer;

    public function setUp()
    {
        $this->fileName = new XlsFileName('foo.xls');
        $this->encoding = new StringLiteral('UTF-8');

        $this->writer = new XlsFileWriter(
            $this->fileName,
            $this->encoding
        );
    }

    /**
     * @test
     */
    public function it_can_return_http_headers_for_streaming_to_a_browser()
    {
        $expected = [
            'Content-Type' => 'application/vnd.ms-excel; charset="UTF-8"',
            'Content-Disposition' => 'attachment; filename="foo.xls"',
        ];

        $actual = $this->writer->getHttpHeaders();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_write_a_variable_number_of_rows_into_a_single_worksheet()
    {
        $rows = [
            [
                'ID',
                'Name',
                'E-mail',
                'Date of birth',
                'Active',
            ],
            [
                5,
                'Foo',
                'foo@mailinator.com',
                '1990-03-01',
                true,
            ],
            [
                7,
                'Bar',
                'bar@mailinator.com',
                '1994-07-12',
                false,
            ],
        ];

        $this->writer->open();
        foreach ($rows as $row) {
            $this->writer->write($row);
        }

        $tempFileName = tempnam(sys_get_temp_dir(), '.xls');
        $handle = fopen($tempFileName, "w");
        fwrite($handle, $this->writer->close());
        fclose($handle);

        $reader = \PHPExcel_IOFactory::createReader('Excel5');
        $excel = $reader->load($tempFileName);
        $excelData = $excel->getActiveSheet()->toArray();

        $this->assertEquals($rows, $excelData);
    }
}
