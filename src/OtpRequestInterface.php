<?php

// Copyright (C) 2021 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii;

use BrokeYourBike\Termii\Enums\PinTypeEnum;
use BrokeYourBike\Termii\Enums\MessageTypeEnum;
use BrokeYourBike\Termii\Enums\ChannelTypeEnum;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
interface OtpRequestInterface
{
    public function getPinId(): ?string;
    public function getFrom(): string;
    public function getTo(): string;
    public function getMessageText(): string;
    public function getMessageType(): MessageTypeEnum;
    public function getChannelType(): ChannelTypeEnum;
    public function getPinType(): PinTypeEnum;
    public function getPinAttempts(): int;
    public function getPinTtlMinutes(): int;
    public function getPinLength(): int;
    public function getPinPlaceholder(): string;
}
