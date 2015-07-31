<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

final class ContactInformation implements \JsonSerializable
{
    /**
     * @var EmailAddress|null
     */
    protected $email;

    /**
     * @var StringLiteral|null
     */
    protected $telephoneNumber;

    /**
     * @var StringLiteral|null
     */
    protected $mobileNumber;

    /**
     * @param EmailAddress $email
     * @return ContactInformation
     */
    public function withEmail(EmailAddress $email)
    {
        return $this->with('email', $email);
    }

    /**
     * @return EmailAddress|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param StringLiteral $telephoneNumber
     * @return ContactInformation
     */
    public function withTelephoneNumber(StringLiteral $telephoneNumber)
    {
        return $this->with('telephoneNumber', $telephoneNumber);
    }

    /**
     * @return StringLiteral|null
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @param StringLiteral $mobileNumber
     * @return ContactInformation
     */
    public function withMobileNumber(StringLiteral $mobileNumber)
    {
        return $this->with('mobileNumber', $mobileNumber);
    }

    /**
     * @return StringLiteral|null
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return ContactInformation
     */
    private function with($property, $value)
    {
        $c = clone $this;
        $c->{$property} = $value;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [];

        if (!is_null($this->email)) {
            $data['email'] = $this->email->toNative();
        }

        if (!is_null($this->telephoneNumber)) {
            $data['telephoneNumber'] = $this->telephoneNumber->toNative();
        }

        if (!is_null($this->mobileNumber)) {
            $data['mobileNumber'] = $this->mobileNumber->toNative();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return self
     */
    public static function fromCultureFeedPassHolder(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        $contactInformation = new self();

        if (!empty($cfPassHolder->email)) {
            $contactInformation = $contactInformation->withEmail(
                new EmailAddress($cfPassHolder->email)
            );
        }

        if (!empty($cfPassHolder->telephone)) {
            $contactInformation = $contactInformation->withTelephoneNumber(
                new StringLiteral($cfPassHolder->telephone)
            );
        }

        if (!empty($cfPassHolder->gsm)) {
            $contactInformation = $contactInformation->withMobileNumber(
                new StringLiteral($cfPassHolder->gsm)
            );
        }

        return $contactInformation;
    }
}
