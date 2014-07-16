<?php

namespace Auth\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Auth\Model\RegistrationEntity;

class RegistrationMapper
{
    protected $tableName = 'oauth_users';
    protected $adapter;
    protected $hydrator;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function insert(RegistrationEntity $data)
    {
        $select = new Select($this->tableName);

        $sql = new \Zend\Db\Sql\Sql($this->adapter);
        $insert = $sql->insert($this->tableName);
        $newData = $data->getArrayCopy();
        /*$newData = array(
            'col1'=> 'val1',
            'col2'=> 'val2',
            'col3'=> 'val3'
        );*/
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        return $this->adapter->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

    }

    /**
     * Gets the value of tableName.
     *
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }
    
    /**
     * Sets the value of tableName.
     *
     * @param mixed $tableName the table name
     *
     * @return self
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @param mixed $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @return mixed
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
}
