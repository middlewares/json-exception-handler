<?php
declare(strict_types = 1);

namespace Middlewares;

use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Safe\json_encode;
use Throwable;
use function WyriHaximus\throwable_encode;

class JsonExceptionHandler implements MiddlewareInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var string */
    private $contentType = 'application/json';

    /** @var bool */
    private $includeTrace = true;

    /** @var int */
    private $jsonOptions = 0;

    public function __construct(
        ?ResponseFactoryInterface $responseFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->responseFactory = $responseFactory ?? Factory::getResponseFactory();
        $this->streamFactory = $streamFactory ?? Factory::getStreamFactory();
    }

    /**
     * Change the content type of the response
     */
    public function contentType(string $type): self
    {
        $this->contentType = $type;

        return $this;
    }

    /**
     * Enable or disable traces in the response
     */
    public function includeTrace(bool $enable): self
    {
        $this->includeTrace = $enable;

        return $this;
    }

    /**
     * Set options for JSON encoding
     *
     * @see http://php.net/manual/function.json-encode.php
     * @see http://php.net/manual/json.constants.php
     */
    public function jsonOptions(int $options): self
    {
        $this->jsonOptions = $options;

        return $this;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

    protected function exceptionResponse(Throwable $e): ResponseInterface
    {
        $exceptionJson = throwable_encode($e);

        if ($this->includeTrace === false) {
            // version 2 (php 7.2 and 7.3)
            /* @phpstan-ignore isset.offset */
            if (isset($exceptionJson['trace'])) {
                $exceptionJson['trace'] = [];
            }

            // version 3 and 4 (7.4, >= 8.0)
            /* @phpstan-ignore isset.offset */
            if (isset($exceptionJson['originalTrace'])) {
                $exceptionJson['originalTrace'] = [];
                $exceptionJson['additionalProperties'] = [
                    'trace' => serialize([]),
                ];
            }
        }

        $exceptionJson = json_encode($exceptionJson, $this->jsonOptions);

        $responseBody = $this->streamFactory->createStream($exceptionJson);

        $response = $this->responseFactory->createResponse(500, 'Internal Server Error');
        $response = $response->withHeader('Content-Type', $this->contentType);
        $response = $response->withBody($responseBody);

        return $response;
    }
}
