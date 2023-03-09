<?php

namespace Pixie\Tests;

use PDO;

class MockPdo extends PDO
{
    public function __construct()
    {
        parent::__construct('');
    }
}