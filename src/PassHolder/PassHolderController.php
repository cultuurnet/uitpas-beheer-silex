<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Hydra\Symfony\PageUrlGenerator;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedCollection;
use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberInvalidException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderController
{
    /**
     * @var PassHolderServiceInterface
     */
    protected $passHolderService;

    /**
     * @var DeserializerInterface
     */
    protected $passHolderJsonDeserializer;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    /**
     * @var QueryBuilderInterface
     */
    protected $searchQuery;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @param PassHolderServiceInterface $passHolderService
     * @param DeserializerInterface $passHolderJsonDeserializer
     * @param DeserializerInterface $registrationJsonDeserializer
     * @param QueryBuilderInterface $searchQuery
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        PassHolderServiceInterface $passHolderService,
        DeserializerInterface $passHolderJsonDeserializer,
        DeserializerInterface $registrationJsonDeserializer,
        QueryBuilderInterface $searchQuery,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->passHolderService = $passHolderService;
        $this->passHolderJsonDeserializer = $passHolderJsonDeserializer;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
        $this->searchQuery = $searchQuery;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws UnknownParameterException
     *   When an unknown query parameter is provided.
     *
     * @throws IncorrectParameterValueException
     *   When a parameter has an invalid value.
     */
    public function search(Request $request)
    {
        $searchQuery = $this->searchQuery;

        foreach ($request->query->all() as $parameter => $value) {
            switch ($parameter) {
                case 'uitpasNumber':
                    if (!is_array($value)) {
                        $value = array($value);
                    }

                    $uitpasNumbers = [];
                    $invalid = [];
                    foreach ($value as $uitpasNumber) {
                        try {
                            $uitpasNumbers[] = new UiTPASNumber($uitpasNumber);
                        } catch (UiTPASNumberInvalidException $e) {
                            $invalid[] = $uitpasNumber;
                        }
                    }

                    if (!empty($invalid)) {
                        throw new IncorrectParameterValueException(
                            'uitpasNumber',
                            'INVALID_UITPAS_NUMBER',
                            $invalid
                        );
                    }

                    $uitpasNumbers = UiTPASNumberCollection::fromArray($uitpasNumbers);

                    $searchQuery = $searchQuery->withUiTPASNumbers(
                        $uitpasNumbers
                    );
                    break;

                case 'page':
                case 'limit':
                    // These are valid but we ignore them for now, we need them
                    // both in 1 method call.
                    break;

                default:
                    throw new UnknownParameterException($parameter);
            }
        }

        // Handle both page and limit parameters together.
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $searchQuery = $searchQuery->withPagination(
            new Integer($page),
            new Integer($limit)
        );

        $resultSet = $this->passHolderService->search($searchQuery);

        $pageUrlGenerator = new PageUrlGenerator(
            $request->query,
            $this->urlGenerator,
            $request->attributes->get('_route')
        );

        $pagedCollection = new PagedCollection(
            $page,
            $limit,
            $resultSet->getResults(),
            $resultSet->getTotal()->toNative(),
            $pageUrlGenerator
        );

        $invalidUitpasNumbers = $resultSet->getInvalidUitpasNumbers();
        if (!is_null($invalidUitpasNumbers)) {
            $pagedCollection = $pagedCollection->withInvalidUitpasNumbers($invalidUitpasNumbers);
        }

        return JsonResponse::create($pagedCollection)
            ->setPrivate();
    }

    /**
     * @param string $uitpasNumber
     * @return JsonResponse

     * @throws PassHolderNotFoundException
     *   When no passholder was found for the provided uitpas number.
     */
    public function getByUitpasNumber($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $passHolder = $this->passHolderService->getByUitpasNumber($uitpasNumber);

        if (is_null($passHolder)) {
            throw new PassHolderNotFoundException();
        }

        return JsonResponse::create()
            ->setData($passHolder)
            ->setPrivate();
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws CompleteResponseException
     *   When a CultureFeed_Exception is encountered.
     */
    public function update(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $passHolder = $this->passHolderJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        try {
            $this->passHolderService->update($uitpasNumber, $passHolder);
        } catch (\CultureFeed_Exception $exception) {
            throw CompleteResponseException::fromCultureFeedException($exception);
        }

        return $this->getByUitpasNumber($uitpasNumber->toNative());
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws CompleteResponseException
     *   When a CultureFeed_Exception is encountered.
     */
    public function register(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        try {
            $this->passHolderService->register(
                $uitpasNumber,
                $registration->getPassHolder(),
                $registration->getVoucherNumber(),
                $registration->getKansenStatuut()
            );
        } catch (\CultureFeed_Exception $exception) {
            throw CompleteResponseException::fromCultureFeedException($exception);
        }

        // Return the registered passholder.
        $passHolder = $this->passHolderService->getByUitpasNumber($uitpasNumber);

        $response = JsonResponse::create()
            ->setData($passHolder)
            ->setPrivate();

        return $response;
    }
}
