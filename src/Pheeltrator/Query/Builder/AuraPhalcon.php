<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 23.01.2018
 * Time: 18:37
 */

namespace TopoTrue\Pheeltrator\Query\Builder;

use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\QueryFactory;
use Phalcon\Db\AdapterInterface;
use TopoTrue\Pheeltrator\Query\Source\Join;


/**
 * Class AuraPhalcon
 * @package TopoTrue\Pheeltrator\Query\AuraPhalcon
 */
class AuraPhalcon implements BuilderInterface
{
    /**
     * @var QueryFactory
     */
    private $factory;
    
    /**
     * @var AdapterInterface
     */
    private $db;
    
    /**
     * @var Select
     */
    private $select;
    
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
     * AuraPhalcon constructor.
     * @param AdapterInterface $db
     */
    public function __construct(AdapterInterface $db)
    {
        $this->db = $db;
        
        $this->factory = new QueryFactory("{$this->db->getType()}");
        
        /** @var Select $select */
        $this->select = $this->factory->newSelect();
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
     * @param array $binds
     * @param array $types
     * @return array
     */
    public function execute(array $binds = [], array $types = [])
    {
        $res = $this->db->query($this->select->getStatement(), $this->select->getBindValues());
        
        $res->setFetchMode(\PDO::FETCH_ASSOC);
        
        return $res->fetchAll();
    }
    
    /**
     * @param string $field
     * @return int
     */
    public function count($field = '*')
    {
        $this->select(["COUNT({$field}) as count"]);
    
        $res     = $this->db->query($this->select->getStatement(), $this->select->getBindValues());
        $numRows = $res->numRows();
    
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
    public function groupBy($groupBy)
    {
        $this->select->groupBy([$groupBy]);
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
     * @return string
     */
    public function getSQL()
    {
        return $this->select->getStatement();
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
     * @return array
     */
    public function getParameters()
    {
        return $this->select->getBindValues();
    }
}
