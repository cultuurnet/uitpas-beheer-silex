<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataValidationController
{

    /**
     * @var DataValidationClientInterface
     */
    protected $dataValidationClient;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * DataValidationController constructor.
     * @param DataValidationClientInterface $dataValidationClient
     * @param ValidatorInterface $validator
     */
    public function __construct(DataValidationClientInterface $dataValidationClient, ValidatorInterface $validator)
    {
        $this->dataValidationClient = $dataValidationClient;
        $this->validator = $validator;
    }

    /**
     * Validate a give email adress
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateEmail(Request $request)
    {
        // Check email query parameter
        if ($email = $request->query->get('email')) {

            // Check if the email is well-formed
            $errors = $this->validator->validate($email, new Email());
            if (count($errors)) {
                return new JsonResponse(['message' => 'The email address is malformed'], 400);
            }

            try {
                // Attempt the email validation
                $validationResult = $this->dataValidationClient->validateEmail($email);

                // The datavalidation service did not return an OK
                // Return a bad response with the reason
                if (!$validationResult->isOK()) {
                    return new JsonResponse(['message' => $validationResult->getReason()], 500);
                }

                return new JsonResponse($validationResult);
            } catch (\Exception $e) {
                // Service error
                return new JsonResponse(['message' => 'Datavalidation service error'], 500);
            }
        }

        return new JsonResponse(['message' => 'The "email" parameter is required'], 400);
    }
}
