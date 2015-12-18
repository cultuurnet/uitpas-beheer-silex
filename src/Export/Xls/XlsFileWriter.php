<?php

namespace CultuurNet\UiTPASBeheer\Export\Xls;

use CultuurNet\UiTPASBeheer\Export\FileWriterInterface;
use ValueObjects\StringLiteral\StringLiteral;

class XlsFileWriter implements FileWriterInterface
{
    const XML_HEADER = '<?xml version="1.0" encoding="%s" ?>';

    const WORKBOOK_START = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:html="http://www.w3.org/TR/REC-html40">';

    const WORKBOOK_END = '</Workbook>';

    const WORKSHEET_START = '<Worksheet ss:Name="%s"><Table>';

    const WORKSHEET_END = '</Table></Worksheet>';

    const ROW_START = '<Row>';

    const ROW_END = '</Row>';

    const CELL_START = '<Cell><Data ss:Type="String">';

    const CELL_END = '</Data></Cell>';

    /**
     * @var XlsFileName
     */
    public $fileName;

    /**
     * @var StringLiteral
     */
    public $encoding;

    /**
     * @var StringLiteral
     */
    public $worksheetTitle;

    /**
     * @param XlsFileName $fileName
     *   Even though we're writing xlsx (xml) data, Excel is only able to open
     *   it with a .xls extension.
     *
     * @param StringLiteral $encoding
     * @param StringLiteral $worksheetTitle
     */
    public function __construct(
        XlsFileName $fileName,
        StringLiteral $encoding = null,
        StringLiteral $worksheetTitle = null
    ) {
        $this->fileName = $fileName;
        $this->encoding = is_null($encoding) ? new StringLiteral('UTF-8') : $encoding;
        $this->worksheetTitle = is_null($worksheetTitle) ? new StringLiteral('Sheet1') : $worksheetTitle;
    }

    /**
     * @inheritdoc
     */
    public function getHttpHeaders()
    {
        $contentType = sprintf(
            'Content-Type: application/vnd.ms-excel; charset="%s"',
            $this->encoding
        );

        $contentDisposition = sprintf(
            'Content-Disposition: inline; filename="%s"',
            $this->fileName->toNative()
        );

        return [
            $contentType,
            $contentDisposition,
        ];
    }

    /**
     * @inheritdoc
     */
    public function open()
    {
        // XML header with encoding.
        $output[] = sprintf(self::XML_HEADER, $this->encoding);

        // Open workbook tag.
        $output[] = self::WORKBOOK_START;

        // Open worksheet tag.
        $output[] = sprintf(
            self::WORKSHEET_START,
            htmlentities(
                $this->worksheetTitle->toNative()
            )
        );

        return implode('', $output);
    }

    /**
     * @inheritdoc
     */
    public function write(array $data)
    {
        $output[] = self::ROW_START;

        foreach ($data as $value) {
            $output[] = $this->writeCell($value);
        }

        $output[] = self::ROW_END;

        return implode('', $output);
    }

    /**
     * @param string $value
     * @return string
     */
    private function writeCell($value)
    {
        $value = (string) $value;

        $value = str_replace(
            '&#039;',
            '&apos;',
            htmlspecialchars(
                $value,
                ENT_QUOTES
            )
        );

        $output[] = self::CELL_START;
        $output[] = $value;
        $output[] = self::CELL_END;

        return implode('', $output);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        // Close worksheet tag.
        $output[] = self::WORKSHEET_END;

        // Close workbook tag.
        $output[] = self::WORKBOOK_END;

        return implode('', $output);
    }
}
