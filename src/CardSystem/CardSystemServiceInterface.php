<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Price\Inquiry;

interface CardSystemServiceInterface
{
    public function getPrice(Inquiry $inquiry);
}
