<?php

// Copyright (C) 2021 Ivan Stasiuk <brokeyourbike@gmail.com>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\Termii\Tests;

use BrokeYourBike\Termii\MessageInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;

/**
 * @author Ivan Stasiuk <brokeyourbike@gmail.com>
 */
abstract class SourceMessageFixture implements MessageInterface, SourceModelInterface
{}
