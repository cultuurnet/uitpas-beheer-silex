<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Identity\UUID;

abstract class PassHolderServiceInterfaceDecoraterBase implements PassHolderServiceInterface
{
    /**
     * @var PassHolderServiceInterface
     */
    protected $decoratee;

    /**
     * PassHolderServiceInterfaceDecoraterBase constructor.
     * @param PassHolderServiceInterface $decoratee
     */
    public function __construct(PassHolderServiceInterface $decoratee)
    {
        $this->decoratee = $decoratee;
    }

    public function search(QueryBuilderInterface $query)
    {
        return $this->decoratee->search($query);
    }

    public function getByUitpasNumber(UiTPASNumber $uitpasNumber)
    {
        return $this->decoratee->getByUitpasNumber($uitpasNumber);
    }

    public function update(UiTPASNumber $uitpasNumber, PassHolder $passHolder)
    {
        return $this->decoratee->update($uitpasNumber, $passHolder);
    }

    public function upgradeCardSystems(
        UiTPASNumber $uitpasNumber,
        CardSystemUpgrade $cardSystemUpgrade
    ) {
        $this->decoratee->upgradeCardSystems(
            $uitpasNumber,
            $cardSystemUpgrade
        );
    }

    public function register(
        UiTPASNumber $uitpasNumber,
        PassHolder $passHolder,
        VoucherNumber $voucherNumber = null,
        KansenStatuut $kansenStatuut = null
    ) {
        return $this->decoratee->register(
            $uitpasNumber,
            $passHolder,
            $voucherNumber,
            $kansenStatuut
        );
    }
}
