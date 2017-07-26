<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use CultuurNet\UiTPASBeheer\DataValidation\Item\EmailValidationResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataValidationControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataValidationController
     */
    protected $controller;

    /**
     * @var DataValidationClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataValidationClient;

    /**
     * @var ValidatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var Constraint
     */
    protected $emailContstraint;

    /**
     * @var ConstraintViolationListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $constraintViolationList;

    /**
     * @var EmailValidationResult
     */
    protected $validationResult;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->dataValidationClient = $this->getMock(DataValidationClientInterface::class);
        $this->validator = $this->getMock(ValidatorInterface::class);

        // Init the controller
        $this->controller = new DataValidationController($this->dataValidationClient, $this->validator);

        $this->emailContstraint = new Email();
        $this->constraintViolationList = new ConstraintViolationList();

        $this->email = 'test@domain.com';
        $this->validationResult = new EmailValidationResult();
        $this->validationResult->setGrade('A');
    }

    /**
     * @test
     */
    public function it_checks_the_required_parameters()
    {
        $response = $this->controller->validateEmail(new Request());

        $this->assertInstanceOf(JsonResponse::class, $response, 'It returns a json response');
        $this->assertEquals(400, $response->getStatusCode(), 'It returns the correct status code');
    }

    /**
     * @test
     */
    public function it_responds_the_validation_result()
    {
        $this->validationResult->setStatus(EmailValidationResult::REALTIME_VALIDATION_RESULT_STATUS_OK);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->email, $this->emailContstraint)
            ->willReturn($this->constraintViolationList);

        $this->dataValidationClient->expects($this->once())
            ->method('validateEmail')
            ->with($this->email)
            ->willReturn($this->validationResult);

        $response = $this->controller->validateEmail(new Request(['email' => $this->email]));

        $this->assertInstanceOf(JsonResponse::class, $response, 'It returns a json response');
        $this->assertEquals(200, $response->getStatusCode(), 'It returns the correct status code');
        $this->assertEquals(json_encode($this->validationResult), $response->getContent(), 'It returns the validation response data');
    }

    /**
     * @test
     */
    public function it_responds_a_validation_result_api_exception()
    {
        $this->validationResult->setStatus(EmailValidationResult::REALTIME_VALIDATION_RESULT_STATUS_ERROR);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->email, $this->emailContstraint)
            ->willReturn($this->constraintViolationList);

        $this->dataValidationClient->expects($this->once())
            ->method('validateEmail')
            ->with($this->email)
            ->willReturn($this->validationResult);

        $response = $this->controller->validateEmail(new Request(['email' => $this->email]));

        $this->assertInstanceOf(JsonResponse::class, $response, 'It returns a json response');
        $this->assertEquals(500, $response->getStatusCode(), 'It returns the correct status code');
    }

    /**
     * @test
     */
    public function it_catches_a_malformed_email_address()
    {
        // Bad email
        $this->email = 'someone@noextension';

        /** @var $constrainViolation ConstraintViolationInterface|\PHPUnit_Framework_MockObject_MockObject */
        $constrainViolation = $this->getMock(ConstraintViolationInterface::class);
        $this->constraintViolationList->add($constrainViolation);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->email, $this->emailContstraint)
            ->willReturn($this->constraintViolationList);

        $response = $this->controller->validateEmail(new Request(['email' => $this->email]));

        $this->assertInstanceOf(JsonResponse::class, $response, 'It returns a json response');
        $this->assertEquals(400, $response->getStatusCode(), 'It returns the correct status code');
    }

    /**
     * @test
     */
    public function it_responds_an_api_error()
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->email, $this->emailContstraint)
            ->willReturn($this->constraintViolationList);

        $this->dataValidationClient->expects($this->once())
            ->method('validateEmail')
            ->with($this->email)
            ->willThrowException(new \Exception('Service error'));

        $response = $this->controller->validateEmail(new Request(['email' => $this->email]));

        $this->assertInstanceOf(JsonResponse::class, $response, 'It returns a json response');
        $this->assertEquals(500, $response->getStatusCode(), 'It returns the correct status code');
    }
}
