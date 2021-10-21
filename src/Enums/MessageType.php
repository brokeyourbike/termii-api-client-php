<?php

// Copyright (C) 2021 Ivan Stasiuk <brokeyourbike@gmail.com>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Enums;

/**
 * @author Ivan Stasiuk <brokeyourbike@gmail.com>
 *
 * @method static MessageType NUMERIC()
 * @method static MessageType ALPHANUMERIC()
 * @method static MessageType PLAIN()
 * @psalm-immutable
 */
final class MessageType extends \MyCLabs\Enum\Enum
{
    private const NUMERIC = 'NUMERIC';
    private const ALPHANUMERIC = 'ALPHANUMERIC';
    private const PLAIN = 'plain';
}
