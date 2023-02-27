<?php

namespace Pixie\Tests;

use PDOStatement;

class MockPDOStatement extends PDOStatement
{
    public array $bindings = [];
    public string $sql = '';
}