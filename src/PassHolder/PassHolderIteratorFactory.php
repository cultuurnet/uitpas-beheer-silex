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
     * @param PassHolderServiceInterface $passHolderService
     */
    public function __construct(
        PassHolderServiceInterface $passHolderService
    ) {
        $this->passHolderService = $passHolderService;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param int $limitPerPage
     * @return \Iterator
     */
    public function search(
        QueryBuilderInterface $queryBuilder,
        $limitPerPage = 20
    ) {
        $preQueryResultSet = $this->passHolderService->search(
            $queryBuilder
                ->withPagination(
                    new Integer(1),
                    new Integer(1)
                )
        );

        $total = $preQueryResultSet->getTotal()->toNative();
        $totalPages = ceil($total / $limitPerPage);
        $currentPage = 0;

        while ($currentPage < $totalPages) {
            $currentPage++;

            $resultSet = $this->passHolderService->search(
                $queryBuilder
                    ->withPagination(
                        new Integer($currentPage),
                        new Integer($limitPerPage)
                    )
            );

            $identities = $resultSet->getResults();

            foreach ($identities as $identity) {
                $primaryUitpasNumber = $identity->getUiTPAS()->getNumber()->toNative();
                $passHolder = $identity->getPassHolder();
                yield $primaryUitpasNumber => $passHolder;
            }
        }
    }
}
