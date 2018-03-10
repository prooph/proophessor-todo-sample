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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface;

use const Webimpress\HttpMiddlewareCompatibility\HANDLER_METHOD;

class JsonPayload implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, HandlerInterface $handler): ResponseInterface
    {
        $contentType = trim($request->getHeaderLine('Content-Type'));

        if (0 === strpos($contentType, 'application/json')) {
            $payload = json_decode((string) $request->getBody(), true);

            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    throw new \RuntimeException('Invalid JSON, maximum stack depth exceeded.', 400);
                case JSON_ERROR_UTF8:
                    throw new \RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded.', 400);
                case JSON_ERROR_SYNTAX:
                case JSON_ERROR_CTRL_CHAR:
                case JSON_ERROR_STATE_MISMATCH:
                    throw new \RuntimeException('Invalid JSON.', 400);
            }

            $request = $request->withParsedBody(null === $payload ? [] : $payload);
        }

        return $handler->{HANDLER_METHOD}($request);
    }
}
