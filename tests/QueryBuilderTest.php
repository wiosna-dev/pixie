<?php

namespace Pixie\Tests;

use PDO;
use Mockery as m;
use Pixie\Exception;
use Pixie\QueryBuilder\QueryBuilderHandler;

class QueryBuilderTest extends TestCase
{
    /**
     * @var QueryBuilderHandler
     */
    protected $builder;

    public function setUp(): void
    {
        parent::setUp();

        $this->builder = new QueryBuilderHandler($this->mockConnection);
    }

    public function testRawQuery(): void
    {
        $query = 'select * from cb_my_table where id = ? and name = ?';
        $bindings = array(5, 'usman');
        $queryArr = $this->builder->query($query, $bindings)->get();
        $this->assertEquals(
            array(
                $query,
                array(array(5, PDO::PARAM_INT), array('usman', PDO::PARAM_STR)),
            ),
            $queryArr
        );
    }

    public function testInsertQueryReturnsIdForInsert(): void
    {
        $this->mockPdoStatement
            ->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(1));

        $this->mockPdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue('11'));

        $id = $this->builder->table('test')->insert(array(
            'id' => 5,
            'name' => 'usman'
        ));

        $this->assertEquals(11, $id);
    }

    public function testInsertQueryReturnsIdForInsertIgnore(): void
    {
        $this->mockPdoStatement
            ->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(1));

        $this->mockPdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue('11'));

        $id = $this->builder->table('test')->insertIgnore(array(
            'id' => 5,
            'name' => 'usman'
        ));

        $this->assertEquals(11, $id);
    }

    public function testInsertQueryReturnsNullForIgnoredInsert(): void
    {
        $this->mockPdoStatement
            ->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(0));

        $id = $this->builder->table('test')->insertIgnore(array(
            'id' => 5,
            'name' => 'usman'
        ));

        $this->assertEquals(null, $id);
    }

    public function testTableNotSpecifiedException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(3);

        $this->builder->where('a', 'b')->get();
    }
}