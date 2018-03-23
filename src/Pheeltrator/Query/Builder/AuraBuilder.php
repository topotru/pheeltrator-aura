<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 24.03.2018
 * Time: 0:08
 */

namespace TopoTrue\Pheeltrator\Query\Builder;

use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\QueryFactory;
use TopoTrue\Pheeltrator\Query\Source\Join;

/**
 * Class AuraBuilder
 * @package TopoTrue\Pheeltrator\Query\Builder
 */
abstract class AuraBuilder implements BuilderInterface
{
    /**
     * @var QueryFactory
     */
    protected $factory;
    
    /**
     * @var Select
     */
    protected $select;
    
    /**
     * @var array
     */
    protected $binds = [];
    
    /**
     * @var array
     */
    protected $types = [];
    
    /**
     * @var array
     */
    protected $having = [];
    
    /**
     * @var bool
     */
    private $initialized = false;
    
    /**
     * AuraPhalcon constructor.
     * @param string $driver_name
     */
    public function __construct(string $driver_name)
    {
        $this->factory = new QueryFactory($driver_name);
        
        /** @var Select $select */
        $this->select = $this->factory->newSelect();
        
        $this->initialized = true;
    }
    
    /**
     * @param array $binds
     * @param array $types
     * @return array
     */
    public function execute(array $binds = [], array $types = [])
    {
        return $this->doExecute($this->select->getStatement(), $this->select->getBindValues());
    }
    
    /**
     * @param string $statement
     * @param array $binds
     * @param array $types
     * @return array
     */
    abstract public function doExecute(string $statement, array $binds = [], array $types = []);
    
    /**
     * @param string $field
     * @return int
     */
    public function count($field = '*')
    {
        $this->select(["COUNT({$field}) as count"]);
        
        $res     = $this->doExecute($this->select->getStatement(), $this->select->getBindValues());
        $numRows = count($res);
        
        $hasAggrHaving = array_filter($this->having, function ($item) {
            return preg_match('/(count|sum|avg|max|min)/i', $item);
        });
        if ($numRows > 1 || $hasAggrHaving) {
            $cnt = $numRows;
        } else {
            //$res->setFetchMode(\PDO::FETCH_ASSOC);
            $row = $res[0];
            $cnt = $row['count'];
        }
        return $cnt;
    }
    
    /**
     * @param string|array $columns
     * @return BuilderInterface
     */
    public function select($columns)
    {
        $this->select->resetCols();
        $columns = array_values((array)$columns);
        $this->select->cols($columns);
        return $this;
    }
    
    /**
     * @param string $from
     * @param string|null $alias
     * @return BuilderInterface
     */
    public function from($from, $alias = null)
    {
        $this->select->from($from.($alias ? " AS {$alias}" : ''));
        return $this;
    }
    
    /**
     * @param string $from
     * @param string $source
     * @param string|null $conditions
     * @param string|null $alias
     * @param string|null $type
     * @return BuilderInterface
     */
    public function join($from, $source, $conditions = null, $alias = null, $type = null)
    {
        $this->select->join($type ?: Join::LEFT, $source.($alias ? " AS {$alias}" : ''), $conditions);
        return $this;
    }
    
    /**
     * @param string $cond
     * @param array|null $bindParams
     * @param array|null $bindTypes
     * @return BuilderInterface
     */
    public function andWhere($cond, $bindParams = null, $bindTypes = null)
    {
        $this->select->where($cond);
        if (is_array($bindParams) && $bindParams) {
            $this->binds = array_merge($this->binds, $bindParams);
            $this->select->bindValues($bindParams);
        }
        if (is_array($bindTypes) && $bindTypes) {
            $this->types = array_merge($this->types, $bindTypes);
        }
        return $this;
    }
    
    /**
     * @param string $cond
     * @param array|null $bindParams
     * @param array|null $bindTypes
     * @return BuilderInterface
     */
    public function andHaving($cond, $bindParams = null, $bindTypes = null)
    {
        $this->select->having($cond);
        if (is_array($bindParams) && $bindParams) {
            $this->binds = array_merge($this->binds, $bindParams);
            $this->select->bindValues($bindParams);
        }
        if (is_array($bindTypes) && $bindTypes) {
            $this->types = array_merge($this->types, $bindTypes);
        }
        $this->having[] = $cond;
        return $this;
    }
    
    /**
     * @param int $limit
     * @param int $offset
     * @return BuilderInterface
     */
    public function limit($limit, $offset = null)
    {
        $this->select
            ->limit($limit)
            ->offset($offset);
        return $this;
    }
    
    /**
     * @param string $orderBy
     * @param string $direction
     * @return BuilderInterface
     */
    public function orderBy($orderBy, $direction)
    {
        $this->select->orderBy(["{$orderBy} {$direction}"]);
        return $this;
    }
    
    /**
     * @param string $groupBy
     * @return BuilderInterface
     */
    public function addGroupBy($groupBy)
    {
        return $this->groupBy($groupBy);
    }
    
    /**
     * @param string $groupBy
     * @return BuilderInterface
     */
    public function groupBy($groupBy)
    {
        $this->select->groupBy([$groupBy]);
        return $this;
    }
    
    /**
     * @return string
     */
    public function getQueryBasicPart()
    {
        $sql = $this->getSQL();
        
        if (false !== $pos1 = stripos($sql, 'FROM ')) {
            $sql = substr($sql, $pos1);
        }
        
        if (false !== $pos2 = stripos($sql, 'ORDER BY')) {
            $sql = substr($sql, 0, $pos2);
        }
        
        return trim($sql);
    }
    
    /**
     * @return string
     */
    public function getSQL()
    {
        return $this->select->getStatement();
    }
    
    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->select->getBindValues();
    }
    
    /**
     * @throws Exception
     */
    public function __destruct()
    {
        if (! $this->initialized) {
            throw new Exception('You need to call parent::__construct() method in constructor');
        }
    }
}
