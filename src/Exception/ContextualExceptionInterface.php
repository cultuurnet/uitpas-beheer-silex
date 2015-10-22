<?php

namespace CultuurNet\UiTPASBeheer\Exception;

interface ContextualExceptionInterface
{
    /**
     * @return mixed
     */
    public function getContext();
}
