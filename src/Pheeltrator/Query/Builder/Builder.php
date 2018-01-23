<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 23.01.2018
 * Time: 18:37
 */

namespace TopoTrue\Pheeltrator\Query\Builder;

use Aura\SqlQuery\QueryFactory;

/**
 * Class Builder
 * @package TopoTrue\Pheeltrator\Query\Builder
 */
class Builder implements BuilderInterface
{
    /**
     * @var QueryFactory
     */
    private $factory;
    
    /**
     * @var
     */
    private $db;
    
    /**
     * @param string|array $columns
     * @return BuilderInterface
     */
    public function select($columns)
    {
        // TODO: Implement select() method.
    }
    
    /**
     * @param string $from
     * @param string $alias
     * @return BuilderInterface
     */
    public function from($from, $alias = null)
    {
        // TODO: Implement from() method.
    }
    
    /**
     * @param string $from
     * @param string $source
     * @param string $conditions
     * @param string $alias
     * @param string $type
     * @return BuilderInterface
     */
    public function join($from, $source, $conditions = null, $alias = null, $type = null)
    {
        // TODO: Implement join() method.
    }
    
    /**
     * @param string $cond
     * @param array $bindParams
     * @param array $bindTypes
     * @return mixed
     */
    public function andWhere($cond, $bindParams = null, $bindTypes = null)
    {
        // TODO: Implement andWhere() method.
    }
    
    /**
     * @param string $cond
     * @param array $bindParams
     * @param array $bindTypes
     * @return mixed
     */
    public function andHaving($cond, $bindParams = null, $bindTypes = null)
    {
        // TODO: Implement andHaving() method.
    }
    
    /**
     * @param array $binds
     * @param array $types
     * @return mixed
     */
    public function execute(array $binds = [], array $types = [])
    {
        // TODO: Implement execute() method.
    }
    
    /**
     * @param string $field
     * @return int
     */
    public function count($field = '*')
    {
        // TODO: Implement count() method.
    }
    
    /**
     * @param int $limit
     * @param int $offset
     * @return BuilderInterface
     */
    public function limit($limit, $offset = null)
    {
        // TODO: Implement limit() method.
    }
    
    /**
     * @param string $orderBy
     * @param string $direction
     * @return BuilderInterface
     */
    public function orderBy($orderBy, $direction)
    {
        // TODO: Implement orderBy() method.
    }
    
    /**
     * @param string $groupBy
     * @return BuilderInterface
     */
    public function groupBy($groupBy)
    {
        // TODO: Implement groupBy() method.
    }
    
    /**
     * @param string $groupBy
     * @return BuilderInterface
     */
    public function addGroupBy($groupBy)
    {
        // TODO: Implement addGroupBy() method.
    }
    
    /**
     * @return string
     */
    public function getSQL()
    {
        // TODO: Implement getSQL() method.
    }
    
    /**
     * @return string
     */
    public function getQueryBasicPart()
    {
        // TODO: Implement getQueryBasicPart() method.
    }
    
    /**
     * @return array
     */
    public function getParameters()
    {
        // TODO: Implement getParameters() method.
    }
}
