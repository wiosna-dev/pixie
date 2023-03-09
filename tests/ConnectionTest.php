<?php

namespace Pixie\Tests;

use Mockery as m;
use PDO;
use Pixie\Connection;
use Pixie\ConnectionAdapters\BaseAdapter;
use Pixie\ConnectionAdapters\Mysql as MysqlAdapter;
use Pixie\QueryBuilder\Adapters\Mysql as MysqlQueryBuilder;

class ConnectionTest extends TestCase
{
    private BaseAdapter|m\MockInterface $mysqlConnectionMock;
    private Connection $connection;

    public function setUp(): void
    {
        parent::setUp();

        $this->mysqlConnectionMock = m::mock(MysqlAdapter::class);
        $this->mysqlConnectionMock->shouldReceive('connect')->andReturn($this->mockPdo);

        $this->container->setInstance('\\Pixie\\ConnectionAdapters\\Mysqlmock', $this->mysqlConnectionMock);
        $this->connection = new Connection('mysqlmock', ['prefix' => 'cb_'], null, $this->container);
    }

    public function testConnection()
    {
        $this->assertEquals($this->mockPdo, $this->connection->getPdoInstance());
        $this->assertInstanceOf(PDO::class, $this->connection->getPdoInstance());
        $this->assertEquals('mysqlmock', $this->connection->getAdapter());
        $this->assertEquals(array('prefix' => 'cb_'), $this->connection->getAdapterConfig());
    }

    public function testQueryBuilderAliasCreatedByConnection()
    {
        $mockQBAdapter = m::mock(MysqlQueryBuilder::class);

        $this->container->setInstance('\\Pixie\\QueryBuilder\\Adapters\\Mysqlmock', $mockQBAdapter);
        $connection = new Connection('mysqlmock', ['prefix' => 'cb_'], 'DBAlias', $this->container);
        $this->assertEquals($this->mockPdo, $connection->getPdoInstance());
        $this->assertInstanceOf('\\Pixie\\QueryBuilder\\QueryBuilderHandler', \DBAlias::newQuery());
    }
}