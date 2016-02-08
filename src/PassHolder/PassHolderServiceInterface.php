<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedResultSet;
use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Identity\UUID;

interface PassHolderServiceInterface
{
    /**
     * @param QueryBuilderInterface $query
     * @return PagedResultSet
     */
    public function search(QueryBuilderInterface $query);

    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return PassHolder|null
     */
    public function getByUitpasNumber(UiTPASNumber $uitpasNumber);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param PassHolder $passHolder
     */
    public function update(UiTPASNumber $uitpasNumber, PassHolder $passHolder);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param CardSystemUpgrade $cardSystemUpgrade
     */
    public function upgradeCardSystems(UiTPASNumber $uitpasNumber, CardSystemUpgrade $cardSystemUpgrade);

    /**
     * Register a new passholder to an existing UiTPAS-number.
     *
     * @param UiTPASNumber $uitpasNumber
     * @param PassHolder $passHolder
     * @param VoucherNumber $voucherNumber
     * @param KansenStatuut $kansenStatuut
     *
     * @return UUID
     *  The UUID assigned to the new passholder.
     **/
    public function register(
        UiTPASNumber $uitpasNumber,
        PassHolder $passHolder,
        VoucherNumber $voucherNumber = null,
        KansenStatuut $kansenStatuut = null
    );
}
