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

namespace Prooph\ProophessorDo\Response;

use Prooph\Psr7Middleware\Response\ResponseStrategy;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;
use Zend\Diactoros\Response\JsonResponse as ZendJsonResponse;

final class JsonResponse implements ResponseStrategy
{
    public function fromPromise(PromiseInterface $promise): ResponseInterface
    {
        $json = null;

        $promise->then(function ($data) use (&$json) {
            $json = $data;
        });

        return new ZendJsonResponse($json);
    }

    public function withStatus(int $statusCode): ResponseInterface
    {
        return new ZendJsonResponse([], $statusCode);
    }
}
