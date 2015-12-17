<?php

namespace CultuurNet\UiTPASBeheer\Export;

class XlsxFileWriter implements FileWriterInterface
{

    const XMLHEADER = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook
        xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
        xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
        xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
        xmlns:html=\"http://www.w3.org/TR/REC-html40\">";

    const XMLFOOTER = "</Workbook>";

    public $encoding = 'UTF-8';

    public $title = 'Sheet1';

    /**
     * @return string
     */
    public function getHttpHeaders()
    {

    }

    public function open()
    {
        // workbook header
        $output = stripslashes(sprintf(self::XMLHEADER, $this->encoding)) . "\n";

        // Set up styles
        $output .= "<Styles>\n";
        $output .= "<Style ss:ID=\"sDT\"><NumberFormat ss:Format=\"Short Date\"/></Style>\n";
        $output .= "</Styles>\n";

        // worksheet header
        $output .= sprintf("<Worksheet ss:Name=\"%s\">\n    <Table>\n", htmlentities($this->title));

        return $output;
    }

    public function write()
    {
        // TODO: Implement write() method.
    }

    /**
     * @return string
     */
    public function close()
    {
        $output = '';

        // worksheet footer
        $output .= "    </Table>\n</Worksheet>\n";

        // workbook footer
        $output .= self::XMLFOOTER;

        return $output;
    }
}
