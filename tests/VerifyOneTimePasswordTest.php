<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;
use BrokeYourBike\Termii\OtpRequestInterface;
use BrokeYourBike\Termii\Enums\PinTypeEnum;
use BrokeYourBike\Termii\Enums\MessageTypeEnum;
use BrokeYourBike\Termii\Enums\ChannelTypeEnum;
use BrokeYourBike\Termii\Client;
use BrokeYourBike\Termii\ApiConfigInterface;
use BrokeYourBike\HasSourceModel\Enums\RequestOptions;

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

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/otp/verify',
            [
                \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'pin_id' => '1234567',
                    'pin' => '000111',
                    'api_key' => $this->publicKey,
                ],
            ],
        ])->once();

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var OtpRequestInterface $mockedOtpRequest */
        $requestResult = $api->verifyOneTimePassword($mockedOtpRequest, '000111');

        $this->assertInstanceOf(ResponseInterface::class, $requestResult);
    }

    /** @test */
    public function it_will_pass_source_model_as_option(): void
    {
        $model = $this->getMockBuilder(SourceOtpRequestFixture::class)->getMock();
        $model->method('getPinId')->willReturn('1234567');
        $model->method('getMessageType')->willReturn(MessageTypeEnum::ALPHANUMERIC);
        $model->method('getChannelType')->willReturn(ChannelTypeEnum::GENERIC);
        $model->method('getPinType')->willReturn(PinTypeEnum::NUMERIC);

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/otp/verify',
            [
                \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'pin_id' => '1234567',
                    'pin' => '000111',
                    'api_key' => $this->publicKey,
                ],
                RequestOptions::SOURCE_MODEL => $model,
            ],
        ])->once();

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var OtpRequestInterface $model */
        $requestResult = $api->verifyOneTimePassword($model, '000111');

        $this->assertInstanceOf(ResponseInterface::class, $requestResult);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
