<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\Registration\Registration;
use ValueObjects\StringLiteral\StringLiteral;

interface MembershipServiceInterface
{
    /**
     * @param StringLiteral $uid
     * @param Registration $registration
     *
     * @return \CultureFeed_Uitpas_Response
     */
    public function register(StringLiteral $uid, Registration $registration);

    /**
     * @param StringLiteral $uid
     * @param AssociationId $associationId
     *
     * @return \CultureFeed_Uitpas_Response
     */
    public function stop(StringLiteral $uid, AssociationId $associationId);
}
