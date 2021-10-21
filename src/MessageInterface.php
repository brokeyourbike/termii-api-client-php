<?php

// Copyright (C) 2021 Ivan Stasiuk <brokeyourbike@gmail.com>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii;

use BrokeYourBike\Termii\Enums\MessageType;
use BrokeYourBike\Termii\Enums\ChannelType;

/**
 * @author Ivan Stasiuk <brokeyourbike@gmail.com>
 */
interface MessageInterface
{
    public function getFrom(): string;
    public function getTo(): string;
    public function getMessageText(): string;
    public function getMessageType(): MessageType;
    public function getChannelType(): ChannelType;
}
