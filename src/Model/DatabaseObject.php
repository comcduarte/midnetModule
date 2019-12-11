<?php
namespace Midnet\Model;

use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Exception;

class DatabaseObject implements InputFilterAwareInterface
{
    const INACTIVE_STATUS = 2;
    const ACTIVE_STATUS = 1;
    
    use AdapterAwareTrait;
    
    protected $table;
    protected $inputFilter;
    protected $private_attributes;
    protected $public_attributes;
    protected $primary_key;
    protected $required;

    public $UUID;
    public $STATUS;
    public $DATE_CREATED;
    public $DATE_MODIFIED;
    
    public function __construct($adapter = null)
    {
        $this->setDbAdapter($adapter);
        
        $this->private_attributes = [
            'adapter',                  //-- From AdapterAwareTrait --//
            'table',
            'inputFilter',
            'private_attributes',
            'public_attributes',
            'primary_key',
            'required',
        ];
        
        $this->setPrimaryKey('UUID');
        
        $this->public_attributes = array_diff(array_keys(get_object_vars($this)), $this->private_attributes);
    }
    
    public function exchangeArray($data)
    {
        foreach ($this->public_attributes as $var) {
            $this->$var = (!empty($data[$var])) ? $data[$var] : null;
        }
    }
    
    public function getArrayCopy()
    {
        $data = NULL;
        foreach ($this->public_attributes as $var) {
            $data[$var] = $this->{$var};
        }
        return $data;
    }

    public function getTableName()
    {
        return $this->table;
    }
    
    public function setTableName($table)
    {
        $this->table = $table;
        return $this;
    }
    
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }
    
    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
        return $this;
    }
    
    public static function retrieveStatus($status) 
    {
        $statuses = [
            NULL => 'Inactive',
            self::INACTIVE_STATUS => 'Inactive',
            self::ACTIVE_STATUS => 'Active',
        ];
        
        return $statuses[$status];
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not Used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
        
            foreach ($this->public_attributes as $var) {
                $inputFilter->add([
                    'name' => $var,
                    'required' => $this->required,
                    'filters' => [
                        ['name' => StripTags::class],
                        ['name' => StringTrim::class],
                    ],
                ]);
            }
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
    
    public function fetchAll(Predicate $predicate = null, array $order = [])
    {
        if ($predicate == null) {
            $predicate = new Where();
        }
        
        $sql = new Sql($this->adapter);
        
        $select = new Select();
        $select->from($this->table);
        $select->where($predicate);
        $select->order($order);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        try {
            $results = $statement->execute();
            $resultSet->initialize($results);
        } catch (Exception $e) {
            return FALSE;
        }
        
        return $resultSet->toArray();
    }

    public function create()
    {
        $date = new \DateTime('now',new \DateTimeZone('EDT'));
        $this->DATE_CREATED = $date->format('Y-m-d H:i:s');
        
        $sql = new Sql($this->adapter);
        $values = $this->getArrayCopy();
        
        $insert = new Insert();
        $insert->into($this->table);
        $insert->values($values);
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        
        $history = new HistoryModel($this->adapter);
        $history->ACTION = "CREATE";
        $history->TABLENAME = $this->getTableName();
        $history->statement = $statement;
        $history->record();
        
        try {
            $statement->execute();
        } catch (Exception $e) {
            return FALSE;
        }
        return TRUE;
    }
    
    public function read(Array $criteria)
    {
        $sql = new Sql($this->adapter);
        
        $select = new Select();
        $select->from($this->table);
        $select->where($criteria);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $resultSet = $statement->execute();
        } catch (Exception $e) {
            return FALSE;
        }
        
        if ($resultSet->getAffectedRows() == 0) {
            return FALSE;
        } else {
            $this->exchangeArray($resultSet->current());
            return TRUE;
        }
    }
    
    public function update()
    {
        $date = new \DateTime('now',new \DateTimeZone('EDT'));
        $this->DATE_MODIFIED = $date->format('Y-m-d H:i:s');
        
        $sql = new Sql($this->adapter);
        $values = $this->getArrayCopy();
        
        $update = new Update();
        $update->table($this->table);
        $update->set($values);
        $update->where([$this->primary_key => $values[$this->primary_key]]);
        
        $statement = $sql->prepareStatementForSqlObject($update);
        
        $history = new HistoryModel($this->adapter);
        $history->ACTION = "UPDATE";
        $history->TABLENAME = $this->getTableName();
        $history->statement = $statement;
        $history->record();
        
        try {
            $statement->execute();
        } catch (Exception $e) {
            return FALSE;
        }
        return TRUE;
    }
    
    public function delete()
    {
        $prikey = $this->primary_key;
        
        $sql = new Sql($this->adapter);
        
        $delete = new Delete();
        $delete->from($this->table)->where(array($prikey => $this->$prikey));
        $statement = $sql->prepareStatementForSqlObject($delete);
        
        $history = new HistoryModel($this->adapter);
        $history->ACTION = "DELETE";
        $history->TABLENAME = $this->getTableName();
        $history->statement = $statement;
        $history->record();
        
        try {
            $statement->execute();
        } catch (Exception $e) {
            return FALSE;
        }
        return TRUE;
    }
}