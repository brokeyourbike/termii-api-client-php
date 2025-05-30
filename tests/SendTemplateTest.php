<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Termii\Interfaces\ApiConfigInterface;
use BrokeYourBike\Termii\Client;
use BrokeYourBike\Termii\Interfaces\TemplateInterface;
use BrokeYourBike\Termii\Models\SendMessageResponse;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class SendTemplateTest extends TestCase
{
    private string $publicKey = 'some-public-key';
    private object $mockedConfig;

    protected function setUp(): void
    {
        $this->mockedConfig = $this->getMockBuilder(ApiConfigInterface::class)->getMock();
        $this->mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $this->mockedConfig->method('getPublicKey')->willReturn($this->publicKey);
    }

    /** @test */
    public function it_can_prepare_request(): void
    {
        $mockedTemplate = $this->getMockBuilder(TemplateInterface::class)->getMock();
        $mockedTemplate->method('getTo')->willReturn('+1234567890');
        $mockedTemplate->method('getDeviceId')->willReturn('device-id-123');
        $mockedTemplate->method('getTemplateId')->willReturn('template-id-456');
        $mockedTemplate->method('getData')->willReturn([
            'name' => 'John',
            'age' => '30',
        ]);

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getHeaders')->willReturn([]);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "code": "ok",
                "balance": 12.34,
                "message_id": "1234567890",
                "message": "Successfully Sent",
                "user": "JOHN DOE",
                "message_id_str": "00001234567890"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->once()->andReturn($mockedResponse);

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var TemplateInterface $mockedTemplate */
        $requestResult = $api->sendTemplate($mockedTemplate);

        $this->assertInstanceOf(SendMessageResponse::class, $requestResult);
        $this->assertEquals('1234567890', $requestResult->messageId);
    }
}
