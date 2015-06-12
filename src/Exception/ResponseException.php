<?php

namespace CultuurNet\UiTPASBeheer\Exception;

/**
 * Used to catch multiple exceptions that are allowed to be detailed
 * in the response at once.
 *
 * Otherwise we'd have to catch all exceptions by catching \Exception,
 * which could potentially reveal internal security issues when returning
 * unforeseen exceptions with JsonErrorResponse.
 */
abstract class ResponseException extends \Exception {}
