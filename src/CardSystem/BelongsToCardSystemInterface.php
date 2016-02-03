<?php

namespace CultuurNet\UiTPASBeheer\CardSystem;

interface BelongsToCardSystemInterface
{
    /**
     * @return CardSystem|null
     */
    public function getCardSystem();
}
