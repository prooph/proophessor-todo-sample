<?php
/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 7/5/15 - 7:31 PM
 */
namespace Prooph\ProophessorDo\App\Commanding;

use Prooph\Common\Messaging\MessageFactory;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\MessageDispatchException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class API
 *
 * @package Prooph\ProophessorDo\App\Commanding
 */
final class API
{
    /**
     * @var \Prooph\ServiceBus\CommandBus
     */
    private $commandBus;

    /**
     * @var MessageFactory
     */
    private $commandFactory;

    /**
     * @param CommandBus $commandBus
     * @param MessageFactory $commandFactory
     */
    public function __construct(CommandBus $commandBus, MessageFactory $commandFactory)
    {
        $this->commandBus = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $commandName = $request->getAttribute('command');

        if (!$commandName) {
            return $next($request, $response);
        }

        try {
            $payload = $this->getPayloadFromRequest($request);

            $command = $this->commandFactory->createMessageFromArray($commandName, ['payload' => $payload]);

            $this->commandBus->dispatch($command);

            return $response->withStatus(202);
        } catch (MessageDispatchException $dispatchException) {
            $e = $dispatchException->getFailedDispatchEvent()->getParam('exception');

            return $this->populateError($response, $e);
        } catch (\Exception $e) {
            return $this->populateError($response, $e);
        }
    }

    /**
     * Get request payload from request object.
     *
     * @todo check $request->getHeaderLine('content-type') ??
     *
     * @param RequestInterface $request
     * @return array
     *
     * @throws \Exception
     */
    private function getPayloadFromRequest(RequestInterface $request)
    {
        $contentType = $request->getHeader('Content-Type');

        if (empty($contentType) || false !== strpos($contentType[0], 'json')) {
            throw new \Exception('Expected Content-Type: ' . $expected);
        }

        $payload = json_decode($request->getBody(), true);

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new \Exception('Invalid JSON, maximum stack depth exceeded.', 400);
            case JSON_ERROR_UTF8:
                throw new \Exception('Malformed UTF-8 characters, possibly incorrectly encoded.', 400);
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_STATE_MISMATCH:
                throw new \Exception('Invalid JSON.', 400);
        }

        return is_null($payload) ? [] : $payload;
    }

    /**
     * @param ResponseInterface $response
     * @param \Exception $e
     * @return ResponseInterface
     */
    private function populateError(ResponseInterface $response, \Exception $e)
    {
        $response = $response->withStatus(500);

        if (getenv('PROOPH_ENV') === 'development') {
            $response->getBody()->write(json_encode(['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Server Error']));
        }

        return $response;
    }
}
