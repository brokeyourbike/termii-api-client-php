<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;
use BrokeYourBike\Termii\MessageInterface;
use BrokeYourBike\Termii\Enums\MessageTypeEnum;
use BrokeYourBike\Termii\Enums\ChannelTypeEnum;
use BrokeYourBike\Termii\Client;
use BrokeYourBike\Termii\ApiConfigInterface;
use BrokeYourBike\HasSourceModel\Enums\RequestOptions;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class SendMessageTest extends TestCase
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
        $mockedMessage = $this->getMockBuilder(MessageInterface::class)->getMock();
        $mockedMessage->method('getFrom')->willReturn('Jane Doe');
        $mockedMessage->method('getTo')->willReturn('John Doe');
        $mockedMessage->method('getMessageText')->willReturn('Hello John!');
        $mockedMessage->method('getMessageType')->willReturn(MessageTypeEnum::ALPHANUMERIC);
        $mockedMessage->method('getChannelType')->willReturn(ChannelTypeEnum::GENERIC);

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/send',
            [
                \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'from' => 'Jane Doe',
                    'to' => 'John Doe',
                    'sms' => 'Hello John!',
                    'type' => MessageTypeEnum::ALPHANUMERIC->value,
                    'channel' => ChannelTypeEnum::GENERIC->value,
                    'api_key' => $this->publicKey,
                ],
            ],
        ])->once();

        /**
         * @var ApiConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($this->mockedConfig, $mockedClient);

        /** @var MessageInterface $mockedMessage */
        $requestResult = $api->sendMessage($mockedMessage);

        $this->assertInstanceOf(ResponseInterface::class, $requestResult);
    }

    /** @test */
    public function it_will_pass_source_model_as_option()
    {
        $model = $this->getMockBuilder(SourceMessageFixture::class)->getMock();
        $model->method('getMessageType')->willReturn(MessageTypeEnum::ALPHANUMERIC);
        $model->method('getChannelType')->willReturn(ChannelTypeEnum::GENERIC);

        /** @var SourceMessageFixture $model */
        $model;

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/sms/send',
            [
                \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'from' => $model->getFrom(),
                    'to' => $model->getTo(),
                    'sms' => $model->getMessageText(),
                    'type' => $model->getMessageType()->value,
                    'channel' => $model->getChannelType()->value,
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
        $requestResult = $api->sendMessage($model);

        $this->assertInstanceOf(ResponseInterface::class, $requestResult);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
