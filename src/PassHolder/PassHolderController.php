<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Hydra\Symfony\PageUrlGenerator;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\Export\FileWriterInterface;
use CultuurNet\UiTPASBeheer\KansenStatuut\Filter\KansenStatuutSpecificationFilter;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\KansenStatuut\Specifications\UsableByCounter;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\MembershipStatus;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\HumanReadableGender;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedCollection;
use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;
use CultuurNet\UiTPASBeheer\Properties\Language;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberInvalidException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\DateTime\Date;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class PassHolderController
{
    /**
     * @var PassHolderServiceInterface
     */
    protected $passHolderService;

    /**
     * @var PassHolderIteratorFactoryInterface
     */
    protected $passHolderIteratorFactory;

    /**
     * @var FileWriterInterface
     */
    protected $exportFileWriter;

    /**
     * @var DeserializerInterface
     */
    protected $passHolderJsonDeserializer;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    protected $cardSystemUpgradeJsonDeserializer;

    /**
     * @var QueryBuilderInterface
     */
    protected $searchQuery;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var \CultureFeed_Uitpas_Counter_Employee
     */
    protected $counter;

    /**
     * @param PassHolderServiceInterface $passHolderService
     * @param PassHolderIteratorFactoryInterface $passHolderIteratorFactory
     * @param FileWriterInterface $exportFileWriter
     * @param DeserializerInterface $passHolderJsonDeserializer
     * @param DeserializerInterface $registrationJsonDeserializer
     * @param DeserializerInterface $cardSystemUpgradeJsonDeserializer
     * @param QueryBuilderInterface $searchQuery
     * @param UrlGeneratorInterface $urlGenerator
     * @param \CultureFeed_Uitpas_Counter_Employee $counter
     */
    public function __construct(
        PassHolderServiceInterface $passHolderService,
        PassHolderIteratorFactoryInterface $passHolderIteratorFactory,
        FileWriterInterface $exportFileWriter,
        DeserializerInterface $passHolderJsonDeserializer,
        DeserializerInterface $registrationJsonDeserializer,
        DeserializerInterface $cardSystemUpgradeJsonDeserializer,
        QueryBuilderInterface $searchQuery,
        UrlGeneratorInterface $urlGenerator,
        \CultureFeed_Uitpas_Counter_Employee $counter
    ) {
        $this->passHolderService = $passHolderService;
        $this->passHolderIteratorFactory = $passHolderIteratorFactory;
        $this->exportFileWriter = $exportFileWriter;
        $this->passHolderJsonDeserializer = $passHolderJsonDeserializer;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
        $this->cardSystemUpgradeJsonDeserializer = $cardSystemUpgradeJsonDeserializer;
        $this->searchQuery = $searchQuery;
        $this->urlGenerator = $urlGenerator;
        $this->counter = $counter;
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
        $searchQuery = $this->getSearchQueryFromQueryParameters(
            $request,
            $this->searchQuery
        );

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

        // These are technically not invalid formatted, but the search just
        // didn't return any results for them.
        $invalidUitpasNumbers = $resultSet->getInvalidUitpasNumbers();
        if (!is_null($invalidUitpasNumbers)) {
            $pagedCollection = $pagedCollection->withInvalidUitpasNumbers($invalidUitpasNumbers);
        }

        return JsonResponse::create($pagedCollection)
            ->setPrivate();
    }

    /**
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Request $request)
    {
        $selection = $request->query->get('selection');

        if (!empty($selection)) {
            $uitpasNumbers = $this->getUitpasNumberCollectionFromQueryParameterValue($selection);
            $searchQuery = $this->searchQuery->withUiTPASNumbers($uitpasNumbers);
        } else {
            $searchQuery = $this->getSearchQueryFromQueryParameters(
                $request,
                $this->searchQuery
            );
        }

        $streamCallback = function () use ($searchQuery) {
            print $this->exportFileWriter->open();
            flush();

            print $this->exportFileWriter->write(
                [
                    'UiTPAS nummer',
                    'Naam',
                    'Voornaam',
                    'Geboortedatum',
                    'Geslacht',
                    'Adres',
                    'Postcode',
                    'Gemeente',
                    'Telefoon',
                    'GSM',
                    'Nationaliteit',
                    'Kansenstatuut einddatum',
                    'ID',
                ]
            );
            flush();

            $passHolders = $this->passHolderIteratorFactory->search($searchQuery);

            $kansenStatuutFilter = new KansenStatuutSpecificationFilter(
                new UsableByCounter($this->counter)
            );

            /* @var PassHolder $passHolder */
            foreach ($passHolders as $uitpasNumber => $passHolder) {
                $contactInformation = $passHolder->getContactInformation();
                $telephoneNumber = '';
                $mobileNumber = '';

                if (!empty($contactInformation)) {
                    $telephoneNumber = $contactInformation->getTelephoneNumber();
                    $mobileNumber = $contactInformation->getMobileNumber();
                }

                $kansenStatuutEndDate = '';
                $kansenStatuten = $passHolder->getKansenStatuten();
                if (!empty($kansenStatuten)) {
                    $filteredKansenStatuten = $kansenStatuutFilter->filter($passHolder->getKansenStatuten());

                    if (!empty($filteredKansenStatuten->length())) {
                        /** @var KansenStatuut $kansenStatuut */
                        $filteredKansenStatuten = $filteredKansenStatuten->toArray();
                        $kansenStatuut = reset($filteredKansenStatuten);
                        $kansenStatuutEndDate = $kansenStatuut->getEndDate()->toNativeDateTime()->format('d-m-Y');
                    }
                }

                $gender = $passHolder->getGender();
                $genderNl = $gender ? new HumanReadableGender($gender, Language::NL()) : '';

                print $this->exportFileWriter->write(
                    [
                        $uitpasNumber,
                        (string) $passHolder->getName()->getLastName(),
                        (string) $passHolder->getName()->getFirstName(),
                        (string) $passHolder->getBirthInformation()->getDate()->toNativeDateTime()->format('d-m-Y'),
                        (string) $genderNl,
                        (string) $passHolder->getAddress()->getStreet(),
                        (string) $passHolder->getAddress()->getPostalCode(),
                        (string) $passHolder->getAddress()->getCity(),
                        (string) $telephoneNumber,
                        (string) $mobileNumber,
                        (string) $passHolder->getNationality(),
                        (string) $kansenStatuutEndDate,
                        (string) $passHolder->getUid(),
                    ]
                );
                flush();
            }

            print $this->exportFileWriter->close();
            flush();
        };

        return new StreamedResponse($streamCallback, 200, $this->exportFileWriter->getHttpHeaders());
    }

    /**
     * @param array|string $value
     *
     * @return UiTPASNumberCollection
     *
     * @throws IncorrectParameterValueException
     */
    private function getUitpasNumberCollectionFromQueryParameterValue($value)
    {
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

        return UiTPASNumberCollection::fromArray($uitpasNumbers);
    }

    /**
     * @param Request $request
     * @param QueryBuilderInterface $searchQuery
     *
     * @return QueryBuilderInterface
     *
     * @throws IncorrectParameterValueException
     * @throws UnknownParameterException
     */
    private function getSearchQueryFromQueryParameters(
        Request $request,
        QueryBuilderInterface $searchQuery
    ) {
        foreach ($request->query->all() as $parameter => $value) {
            switch ($parameter) {
                case 'uitpasNumber':
                    $uitpasNumbers = $this->getUitpasNumberCollectionFromQueryParameterValue($value);
                    $searchQuery = $searchQuery->withUiTPASNumbers(
                        $uitpasNumbers
                    );
                    break;

                case 'dateOfBirth':
                    $date = \DateTime::createFromFormat('Y-m-d', $value);
                    if (!$date) {
                        throw new IncorrectParameterValueException('dateOfBirth');
                    }

                    $searchQuery = $searchQuery->withDateOfBirth(
                        Date::fromNativeDateTime($date)
                    );
                    break;

                case 'firstName':
                    $searchQuery = $searchQuery->withFirstName(
                        new StringLiteral((string) $value)
                    );
                    break;

                case 'name':
                    $searchQuery = $searchQuery->withName(
                        new StringLiteral((string) $value)
                    );
                    break;

                case 'street':
                    $searchQuery = $searchQuery->withStreet(
                        new StringLiteral((string) $value)
                    );
                    break;

                case 'city':
                    $searchQuery = $searchQuery->withCity(
                        new StringLiteral((string) $value)
                    );
                    break;

                case 'email':
                    try {
                        $searchQuery = $searchQuery->withEmail(
                            new EmailAddress((string) $value)
                        );
                    } catch (InvalidNativeArgumentException $e) {
                        throw new IncorrectParameterValueException('email');
                    }
                    break;

                case 'membershipAssociationId':
                    $searchQuery = $searchQuery->withAssociationId(
                        new AssociationId((string) $value)
                    );
                    break;

                case 'membershipStatus':
                    try {
                        $searchQuery = $searchQuery->withMembershipStatus(
                            MembershipStatus::get($value)
                        );
                    } catch (\InvalidArgumentException $e) {
                        throw new IncorrectParameterValueException('membershipStatus');
                    }
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

        return $searchQuery;
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
                $registration->getKansenStatuut(),
                $registration->getSchoolConsumerKey(),
                $registration->hasLegalTermsPaper(),
                $registration->hasLegalTermsDigital(),
                $registration->hasParentalConsent()
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

    /**
     * @param Request $request
     * @param string $uitpasNumber
     * @return Response
     */
    public function upgradeCardSystems(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $cardSystemUpgrade = $this->cardSystemUpgradeJsonDeserializer
            ->deserialize(
                new StringLiteral($request->getContent())
            );

        $this->passHolderService->upgradeCardSystems(
            $uitpasNumber,
            $cardSystemUpgrade
        );

        return new Response();
    }
}
