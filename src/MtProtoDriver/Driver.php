<?php

namespace MtProtoDriver;

use Api;

abstract class Driver  implements Api
{
    protected string $sessionDir = 'clientSession';
}