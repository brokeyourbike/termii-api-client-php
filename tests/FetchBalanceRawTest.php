<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Termii\Models\FetchBalanceResponse;
use BrokeYourBike\Termii\Interfaces\ApiConfigInterface;
use BrokeYourBike\Termii\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class FetchBalanceRawTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_prepare_request(): void
    {
        $publicKey = 'some-public-key';

        $mockedConfig = $this->getMockBuilder(ApiConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getPublicKey')->willReturn($publicKey);

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "user": "JOHN DOE",
                "balance": 4662.3,
                "currency": "NGN"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'GET',
            'https://api.example/get-balance',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::QUERY => [
                    'api_key' => $publicKey,
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);
        $response = $api->fetchBalanceRaw();

        $this->assertInstanceOf(FetchBalanceResponse::class, $response);
        $this->assertSame('JOHN DOE', $response->user);
        $this->assertSame(4662.3, $response->balance);
        $this->assertSame('NGN', $response->currency);
    }
}
