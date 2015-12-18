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
    public function search(QueryBuilderInterface $queryBuilder) {
        $preQueryResultSet = $this->passHolderService->search(
            $queryBuilder
                ->withPagination(
                    new Integer(1),
                    new Integer(1)
                )
        );

        $total = $preQueryResultSet->getTotal()->toNative();
        $totalPages = ceil($total / $this->limitPerPage);
        $currentPage = 0;

        while ($currentPage < $totalPages) {
            $currentPage++;

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
        }
    }
}
