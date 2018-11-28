<?php 
namespace Midnet\Form\Element;

use Zend\Db\Sql\Sql as Sql;
use Zend\Db\Sql\Select as SqlSelect;
use Zend\Form\Element\Select;
use RuntimeException;
use Midnet\Traits\AdapterTrait;

class DatabaseSelectObject extends Select 
{
    use AdapterTrait;
    
    protected $database_table;
    protected $database_id_column;
    protected $database_value_column;
    
    public function init()
    {
        $valueOptions = [
            1 => 'One',
            2 => 'Two',
        ];
        
        $this->setValueOptions($valueOptions);
    }
    
    public function populateElement()
    {
        $sql = new Sql($this->adapter);
        
        $select = new SqlSelect();
        $select->from($this->database_table);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $resultSet = $statement->execute();
        } catch (RuntimeException $e) {
            return $e;
        }
        
        foreach ($resultSet as $id => $object) {
            $options[$id] = $object;
        }
        
        $this->setValueOptions($options);
    }
    
    public function setOptions($options)
    {
        parent::setOptions($options);
        
        if (isset($options['database_table'])) {
            $this->setDatabase_table($options['database_table']);
        }
        
        if (isset($options['database_id_column'])) {
            $this->setDatabase_id_column($options['database_id_column']);
        }
        
        if (isset($options['database_value_column'])) {
            $this->setDatabase_value_column($options['database_value_column']);
        }
        
        $this->populateElement();
        
        return $this;
    }
    
    public function getDatabase_table()
    {
        return $this->database_table;
    }

    public function setDatabase_table($database_table)
    {
        $this->database_table = $database_table;
    }

    public function getDatabase_id_column()
    {
        return $this->database_id_column;
    }

    public function setDatabase_id_column($database_id_column)
    {
        $this->database_id_column = $database_id_column;
    }
    
    public function getDatabase_value_column()
    {
        return $this->database_value_column;
    }
    
    public function setDatabase_value_column($database_value_column)
    {
        $this->database_value_column = $database_value_column;
    }
}