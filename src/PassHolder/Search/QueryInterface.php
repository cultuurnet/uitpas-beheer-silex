<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\MembershipStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

interface QueryInterface
{
    /**
     * @param UiTPASNumberCollection $uitpasNumbers
     *
     * @return static
     */
    public function withUiTPASNumbers(UiTPASNumberCollection $uitpasNumbers);

    /**
     * @return UiTPASNumberCollection|null
     */
    public function getUiTPASNumbers();

    /**
     * @param Date $dateOfBirth
     *
     * @return static
     */
    public function withDateOfBirth(Date $dateOfBirth);

    /**
     * @param StringLiteral $firstName
     *
     * @return static
     */
    public function withFirstName(StringLiteral $firstName);

    /**
     * @param StringLiteral $name
     *
     * @return static
     */
    public function withName(StringLiteral $name);

    /**
     * @param StringLiteral $street
     *
     * @return static
     */
    public function withStreet(StringLiteral $street);

    /**
     * @param StringLiteral $city
     *
     * @return static
     */
    public function withCity(StringLiteral $city);

    /**
     * @param EmailAddress $email
     *
     * @return static
     */
    public function withEmail(EmailAddress $email);

    /**
     * @param AssociationId $associationId
     *
     * @return static
     */
    public function withAssociationId(AssociationId $associationId);

    /**
     * @param MembershipStatus $membershipStatus
     *
     * @return static
     */
    public function withMembershipStatus(MembershipStatus $membershipStatus);

    /**
     * @param \ValueObjects\Number\Integer $page
     * @param \ValueObjects\Number\Integer $limit
     *
     * @return static
     */
    public function withPagination(Integer $page, Integer $limit);
}
