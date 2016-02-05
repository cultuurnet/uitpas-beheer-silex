<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;
use ValueObjects\Number\Integer;

class PassHolderIteratorFactory implements PassHolderIteratorFactoryInterface
{
    /**
     * @var PassHolderServiceInterface
     */
    private $passHolderService;

    /**
     * @var int
     */
    private $limitPerPage;

    /**
     * @param PassHolderServiceInterface $passHolderService
     * @param int $limitPerPage
     */
    public function __construct(
        PassHolderServiceInterface $passHolderService,
        $limitPerPage = 20
    ) {
        $this->passHolderService = $passHolderService;
        $this->limitPerPage = $limitPerPage;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @return \Iterator
     */
    public function search(QueryBuilderInterface $queryBuilder)
    {
        $currentPage = 1;

        do {
            $resultSet = $this->passHolderService->search(
                $queryBuilder
                    ->withPagination(
                        new Integer($currentPage),
                        new Integer($this->limitPerPage)
                    )
            );

            $identities = $resultSet->getResults();

            foreach ($identities as $identity) {
                $primaryUitpasNumber = $identity->getUiTPAS()->getNumber()->toNative();
                $passHolder = $identity->getPassHolder();
                yield $primaryUitpasNumber => $passHolder;
            }

            $count = $currentPage * $this->limitPerPage;
            $currentPage++;
            $total = $resultSet->getTotal()->toNative();
        } while ($count < $total);
    }
}
