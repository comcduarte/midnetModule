<?php
namespace Midnet\Model;

use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Exception;

class HistoryModel 
{
    use AdapterAwareTrait;
    
    public $UUID;
    public $ACTION;
    public $USER;
    public $DATETIME;
    public $ORIG_VALUE;
    public $NEW_VALUE;
    public $TABLENAME;
    public $FIELD;
    public $QUERY;
    public $statement;
    
    public function __construct($adapter = NULL)
    {
        $this->setDbAdapter($adapter);
        
        $uuid = new Uuid();
        $this->UUID = $uuid->value;
        
        $date = new \DateTime('now',new \DateTimeZone('EDT'));
        $this->DATETIME = $date->format('Y-m-d H:i:s');
    }
    
    public function record()
    {
        $sql = new Sql($this->adapter);
        
        $insert = new Insert();
        $insert->into('history');
        $params = $this->statement->getParameterContainer()->getPositionalArray();
        $sqlstring = $this->statement->getSql();
        $formattedstring = preg_replace('/:[c\_,where]+[0-9]+/', '%s', $sqlstring);
        $query = vsprintf($formattedstring, $params);
        
        
        $insert->columns([
            'UUID',
            'ACTION',
            'USER',
            'DATETIME',
            'TABLENAME',
            'QUERY',
        ])->values([
            $this->UUID,
            $this->ACTION,
            $this->USER,
            $this->DATETIME,
            $this->TABLENAME,
            $query,
        ]);
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        
        try {
            $statement->execute();
        } catch (Exception $e) {
            return FALSE;
        }
        return TRUE;
    }
    
    public function getArrayCopy()
    {
        $data = NULL;
        foreach ($this->public_attributes as $var) {
            $data[$var] = $this->{$var};
        }
        return $data;
    }
}