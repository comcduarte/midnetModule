<?php 
namespace Midnet\Traits;

use Zend\Db\Adapter\Adapter;

trait AdapterTrait
{
    protected $adapter;
    
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
}