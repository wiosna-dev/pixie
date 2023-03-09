<?php

namespace Pixie\Tests;

use Mockery as m;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Pixie\Connection;
use Pixie\EventHandler;
use Viocon\Container;

class TestCase extends BaseTestCase
{
    /**
     * @var Container
     */
    protected Container $container;
    protected $mockConnection;
    protected \PDO|MockObject $mockPdo;
    protected PDOStatement|MockObject $mockPdoStatement;

    public function setUp(): void
    {
        $this->container = new Container();

        $this->mockPdoStatement = $this->createMock(MockPDOStatement::class);

        $mockPdoStatement = $this->mockPdoStatement;

        $mockPdoStatement->bindings = [];

        $this->mockPdoStatement
            ->expects($this->any())
            ->method('bindValue')
            ->will($this->returnCallback(function ($parameter, $value, $dataType) use ($mockPdoStatement) {
                $mockPdoStatement->bindings[] = [$value, $dataType];

                return true;
            }));

        $this->mockPdoStatement
            ->expects($this->any())
            ->method('execute')
            ->will($this->returnCallback(function($bindings = null) use ($mockPdoStatement) {
                if ($bindings) {
                    $mockPdoStatement->bindings = $bindings;
                }

                return true;
            }));


        $this->mockPdoStatement
            ->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnCallback(function() use ($mockPdoStatement) {
                return [$mockPdoStatement->sql, $mockPdoStatement->bindings];
            }));

//        $this->mockPdo = $this->getMock(PDO::class, array('prepare', 'setAttribute', 'quote', 'lastInsertId'));
        $this->mockPdo = $this->createMock(PDO::class);

        $this->mockPdo
            ->expects($this->any())
            ->method('prepare')
            ->will($this->returnCallback(function($sql) use ($mockPdoStatement) {
                $mockPdoStatement->sql = $sql;

                return $mockPdoStatement;
            }));

        $this->mockPdo
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function($value){
                return "'$value'";
            }));

        $eventHandler = new EventHandler();

        $this->mockConnection = m::mock(Connection::class);
        $this->mockConnection->shouldReceive('getPdoInstance')->andReturn($this->mockPdo);
        $this->mockConnection->shouldReceive('getAdapter')->andReturn('mysql');
        $this->mockConnection->shouldReceive('getAdapterConfig')->andReturn(array('prefix' => 'cb_'));
        $this->mockConnection->shouldReceive('getContainer')->andReturn($this->container);
        $this->mockConnection->shouldReceive('getEventHandler')->andReturn($eventHandler);
    }

    public function tearDown(): void
    {
        m::close();
    }

    public function callbackMock()
    {
        $args = func_get_args();

        return count($args) == 1 ? $args[0] : $args;
    }
}

