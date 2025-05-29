<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Models;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class SendTemplateResponse extends JsonResponse
{
    /**
     * @var MessageResultData[]
     */
    public array $data;

    public function __construct(ResponseInterface $response)
    {
        $decoded = \json_decode($response->getBody(), true);
        $wrappedBody = \json_encode(['data' => $decoded]);

        if ($wrappedBody === false) {
            throw new \RuntimeException('Failed to encode response body: ' . json_last_error_msg());
        }

        $wrappedResponse = new GuzzleResponse(
            $response->getStatusCode(),
            $response->getHeaders(),
            $wrappedBody
        );

        parent::__construct($wrappedResponse);
    }
}

class MessageResultData extends DataTransferObject
{
    public string $code;

    #[MapFrom('message_id')]
    public ?string $messageId;

    public ?string $message;
}