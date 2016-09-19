<?php

namespace CultuurNet\UiTPASBeheer\Export\Xls;

use CultuurNet\UiTPASBeheer\Export\FileWriterInterface;
use ValueObjects\StringLiteral\StringLiteral;

class XlsFileWriter implements FileWriterInterface
{

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
     * @var PHP_Excell
     */
    public $excel;

    /**
     * @var StringLiteral
     */
    public $exportType;

    /**
     * @var int
     */
    private $currentRow = 1;

    /**
     * @param XlsFileName $fileName
     *   Even though we're writing xlsx (xml) data, Excel is only able to open
     *   it with a .xls extension.
     *
     * @param StringLiteral $encoding
     * @param StringLiteral $worksheetTitle
     * @param StringLiteral $exportType
     */
    public function __construct(
        XlsFileName $fileName,
        StringLiteral $encoding = null,
        StringLiteral $worksheetTitle = null,
        StringLiteral $exportType = null
    ) {
        $this->fileName = $fileName;
        $this->encoding = is_null($encoding) ? new StringLiteral('UTF-8') : $encoding;
        $this->worksheetTitle = is_null($worksheetTitle) ? new StringLiteral('Sheet1') : $worksheetTitle;
        $this->exportType = is_null($exportType) ? new StringLiteral('Excel5') : $exportType;
    }

    /**
     * @inheritdoc
     */
    public function getHttpHeaders()
    {
        $contentType = sprintf(
            'application/vnd.ms-excel; charset="%s"',
            $this->encoding
        );

        $contentDisposition = sprintf(
            'attachment; filename="%s"',
            $this->fileName->toNative()
        );

        return [
            'Content-Type' => $contentType,
            'Content-Disposition' => $contentDisposition,
        ];
    }

    public function open()
    {
        $this->excel = new \PHPExcel();
        $this->excel->getActiveSheet()->setTitle($this->worksheetTitle->toNative());
        return '';
    }

    public function close()
    {

        ob_start();
        $writer = \PHPExcel_IOFactory::createWriter($this->excel, $this->exportType);
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * @inheritdoc
     */
    public function write(array $data)
    {
        foreach ($data as $column => $data) {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($column, $this->currentRow, $data);
        }

        $this->currentRow++;

    }
}
