<?php 
namespace Midnet\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Midnet\Model\DatabaseObject;

class AbstractBaseForm extends Form
{
    public function initialize()
    {
        $this->add([
            'name' => 'UUID',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'UUID',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'UUID',
            ],
        ],['priority' => 0]);
        
        $this->add([
            'name' => 'STATUS',
            'type' => Select::class,
            'attributes' => [
                'id' => 'STATUS',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    DatabaseObject::INACTIVE_STATUS => 'Inactive',
                    DatabaseObject::ACTIVE_STATUS => 'Active',
                ],
            ],
        ],['priority' => 10]);
        
        $this->add(new Csrf('SECURITY'),['priority' => 0]);
        
        $this->add([
            'name' => 'SUBMIT',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary form-control mt-4',
                'id' => 'SUBMIT',
            ],
        ],['priority' => 0]);
        
    }
}