<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\MembershipStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class Query implements QueryBuilderInterface
{
    /**
     * @var UiTPASNumberCollection|null
     */
    protected $uitpasNumbers;

    /**
     * @var Date
     */
    protected $dateOfBirth;

    /**
     * @var StringLiteral
     */
    protected $firstName;

    /**
     * @var StringLiteral
     */
    protected $name;

    /**
     * @var StringLiteral
     */
    protected $street;

    /**
     * @var StringLiteral
     */
    protected $city;

    /**
     * @var EmailAddress
     */
    protected $email;

    /**
     * @var AssociationId
     */
    protected $associationId;

    /**
     * @var MembershipStatus
     */
    protected $membershipStatus;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $page;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $limit;

    public function __construct()
    {
        $this->page = new Integer(1);
        $this->limit = new Integer(10);
    }

    /**
     * @param UiTPASNumberCollection $uitpasNumbers
     * @return static
     */
    public function withUiTPASNumbers(UiTPASNumberCollection $uitpasNumbers)
    {
        $c = clone $this;
        $c->uitpasNumbers = $uitpasNumbers;
        return $c;
    }

    /**
     * @return UiTPASNumberCollection|null
     */
    public function getUiTPASNumbers()
    {
        return $this->uitpasNumbers;
    }

    /**
     * @param \ValueObjects\Number\Integer $page
     * @param \ValueObjects\Number\Integer $limit
     * @return static
     */
    public function withPagination(Integer $page, Integer $limit)
    {
        $c = clone $this;
        $c->page = $page;
        $c->limit = $limit;
        return $c;
    }

    /**
     * @param Date $dateOfBirth
     * @return static
     */
    public function withDateOfBirth(Date $dateOfBirth)
    {
        $c = clone $this;
        $c->dateOfBirth = $dateOfBirth;
        return $c;
    }

    /**
     * @param StringLiteral $firstName
     * @return static
     */
    public function withFirstName(StringLiteral $firstName)
    {
        $c = clone $this;
        $c->firstName = $firstName;
        return $c;
    }

    /**
     * @param StringLiteral $name
     * @return static
     */
    public function withName(StringLiteral $name)
    {
        $c = clone $this;
        $c->name = $name;
        return $c;
    }

    /**
     * @param StringLiteral $street
     * @return static
     */
    public function withStreet(StringLiteral $street)
    {
        $c = clone $this;
        $c->street = $street;
        return $c;
    }

    /**
     * @param StringLiteral $city
     * @return static
     */
    public function withCity(StringLiteral $city)
    {
        $c = clone $this;
        $c->city = $city;
        return $c;
    }

    /**
     * @param EmailAddress $email
     * @return static
     */
    public function withEmail(EmailAddress $email)
    {
        $c = clone $this;
        $c->email = $email;
        return $c;
    }

    /**
     * @param AssociationId $associationId
     * @return static
     */
    public function withAssociationId(AssociationId $associationId)
    {
        $c = clone $this;
        $c->associationId = $associationId;
        return $c;
    }

    /**
     * @param MembershipStatus $membershipStatus
     * @return static
     */
    public function withMembershipStatus(MembershipStatus $membershipStatus)
    {
        $c = clone $this;
        $c->membershipStatus = $membershipStatus;
        return $c;
    }

    /**
     * @return \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions
     */
    public function build()
    {
        $searchOptions = new \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        $searchOptions->max = $this->limit->toNative();
        $searchOptions->start = ($this->page->toNative() - 1) * $this->limit->toNative();
        $searchOptions->includeBlocked = true;

        if (!is_null($this->uitpasNumbers)) {
            $searchOptions->uitpasNumber = array_map(
                function (UiTPASNumber $uitpasNumber) {
                    return $uitpasNumber->toNative();
                },
                array_values($this->uitpasNumbers->toArray())
            );
        }

        if (!is_null($this->dateOfBirth)) {
            $searchOptions->dob = $this->dateOfBirth->toNativeDateTime()->getTimestamp();
        }
        if (!is_null($this->firstName)) {
            $searchOptions->firstName = $this->firstName->toNative();
        }
        if (!is_null($this->name)) {
            $searchOptions->name = $this->name->toNative();
        }
        if (!is_null($this->street)) {
            $searchOptions->street = $this->street->toNative();
        }
        if (!is_null($this->city)) {
            $searchOptions->city = $this->city->toNative();
        }
        if (!is_null($this->email)) {
            $searchOptions->email = $this->email->toNative();
        }

        if (!is_null($this->associationId)) {
            $searchOptions->associationId = $this->associationId->toNative();
            $searchOptions->expiredMemberships = 'BOTH';
        }

        if (!is_null($this->membershipStatus)) {
            $searchOptions->expiredMemberships = $this->membershipStatus->toNative();
        }

        return $searchOptions;
    }
}
