<?php
namespace Wall\V1\Rest\Wall; 

use Zend\Db\Sql\Select; 
use Zend\Db\Adapter\AdapterInterface; 
use Zend\Paginator\Adapter\DbSelect; 

class WallMapper {

    protected $adapter;

    /* public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    } */

    public function fetchAll()
    {
        $select = new Select('users');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new WallCollection($paginatorAdapter);
        return $collection;
    }

    public function fetchOne($albumId)
    {
        $sql = 'SELECT * FROM user WHERE id = ?';
        $resultset = $this->adapter->query($sql, array($albumId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new WallEntity();
        $entity->album_id = $data[0]['album_id'];
        $entity->artist  = $data[0]['artist'];
        $entity->title   = $data[0]['title'];
        return $entity;
    }

    public function setDbAdapter($adapter)
    {
        $this->adapter= $adapter;   
    }

    public function getDbAdapter()
    {
        return $this->adapter;
    }
}
