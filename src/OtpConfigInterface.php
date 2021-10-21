<?php

// Copyright (C) 2021 Ivan Stasiuk <brokeyourbike@gmail.com>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii;

use App\Enums\Termii\PinType;
use App\Enums\Termii\MessageType;
use App\Enums\Termii\ChannelType;

/**
 * @author Ivan Stasiuk <brokeyourbike@gmail.com>
 */
interface OtpConfigInterface
{
    public function getFrom(): string;
    public function getChannelType(): ChannelType;
    public function getMessageType(): MessageType;
    public function getPinType(): PinType;
    public function getPinAttempts(): int;
    public function getPinTtlMinutes(): int;
    public function getPinLength(): int;
    public function getPinPlaceholder(): string;
    public function getPinMessage(): string;
}
