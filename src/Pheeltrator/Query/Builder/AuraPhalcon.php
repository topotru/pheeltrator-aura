<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 23.01.2018
 * Time: 18:37
 */

namespace TopoTrue\Pheeltrator\Query\Builder;

use Phalcon\Db\AdapterInterface;

/**
 * Class AuraPhalcon
 * @package TopoTrue\Pheeltrator\Query\AuraPhalcon
 */
class AuraPhalcon extends AuraBuilder
{
    /**
     * @var AdapterInterface
     */
    protected $db;
    
    /**
     * AuraPhalcon constructor.
     * @param AdapterInterface $db
     */
    public function __construct(AdapterInterface $db)
    {
        parent::__construct("{$this->db->getType()}");
        
        $this->db = $db;
    }
    
    /**
     * @param string $statement
     * @param array $binds
     * @param array $types
     * @return array
     */
    public function doExecute(string $statement, array $binds = [], array $types = [])
    {
        $res = $this->db->query($statement, $binds, $types);
        
        $res->setFetchMode(\PDO::FETCH_ASSOC);
    
        return $res->fetchAll();
    }
}
