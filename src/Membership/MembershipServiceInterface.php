<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use ValueObjects\StringLiteral\StringLiteral;

interface MembershipServiceInterface
{
    /**
     * @return AssociationCollection $associationCollection
     */
    public function getAssociations();

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
