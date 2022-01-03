<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Termii\Models\VerifyOneTimePasswordResponse;
use BrokeYourBike\Termii\Interfaces\OtpRequestInterface;
use BrokeYourBike\Termii\Interfaces\ApiConfigInterface;
use BrokeYourBike\Termii\Enums\PinTypeEnum;
use BrokeYourBike\Termii\Enums\MessageTypeEnum;
use BrokeYourBike\Termii\Enums\ChannelTypeEnum;
use BrokeYourBike\Termii\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class VerifyOneTimePasswordTest extends TestCase
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
        $mockedOtpRequest = $this->getMockBuilder(OtpRequestInterface::class)->getMock();
        $mockedOtpRequest->method('getPinId')->willReturn('1234567');
        $mockedOtpRequest->method('getMessageType')->willReturn(MessageTypeEnum::ALPHANUMERIC);
        $mockedOtpRequest->method('getChannelType')->willReturn(ChannelTypeEnum::GENERIC);
        $mockedOtpRequest->method('getPinType')->willReturn(PinTypeEnum::NUMERIC);

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn(' {
                "pinId": "c8dcd048-5e7f-4347-8c89-4470c3af0b",
                "verified": "True",
                "msisdn": "2348109077743"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/otp/verify',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'pin_id' => '1234567',
                    'pin' => '000111',
                    'api_key' => $this->publicKey,
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var OtpRequestInterface $mockedOtpRequest */
        $requestResult = $api->verifyOneTimePassword($mockedOtpRequest, '000111');

        $this->assertInstanceOf(VerifyOneTimePasswordResponse::class, $requestResult);
    }
}
