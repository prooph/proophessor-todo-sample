<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\ProophessorDo\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Prooph\Psr7Middleware\Exception\RuntimeException;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface;
use Zend\Diactoros\Response\JsonResponse;

use const Webimpress\HttpMiddlewareCompatibility\HANDLER_METHOD;

/**
 * Error middleware to handle json requests
 */
final class JsonError implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, HandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->{HANDLER_METHOD}($request);
        } catch (\Throwable $e) {
            $contentType = trim($request->getHeaderLine('Content-Type'));

            if ($e instanceof RuntimeException) {
                $e = $e->getPrevious();

                if ($e instanceof CommandDispatchException) {
                    $e = $e->getPrevious();
                }
            }

            if (0 === mb_strpos($contentType, 'application/json')) {
                $data = 'development' === getenv('PROOPH_ENV')
                    ? ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
                    : ['message' => 'Server Error'];

                return new JsonResponse($data, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
