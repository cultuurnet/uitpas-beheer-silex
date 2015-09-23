<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use ValueObjects\StringLiteral\StringLiteral;

class KansenStatuutJsonDeserializer extends JSONDeserializer
{
    /**
     * @var KansenStatuutEndDateJSONDeserializer
     */
    private $endDateDeserializer;

    public function __construct()
    {
        $this->endDateDeserializer = new KansenStatuutEndDateJSONDeserializer();

    }

    /**
     * @param StringLiteral $data
     * @return KansenStatuut
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $endDate = $this->endDateDeserializer->deserialize($data);
        $kansenStatuut = new KansenStatuut(
            $endDate
        );

        $data = parent::deserialize($data);

        if (isset($data->remarks)) {
            $remarks = new Remarks($data->remarks);
            $kansenStatuut = $kansenStatuut->withRemarks($remarks);
        }

        return $kansenStatuut;
    }
}
