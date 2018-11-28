<?php 
namespace Midnet\Form\Element\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Midnet\Form\Element\DatabaseSelectObject;

class DatabaseSelectObjectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $element = new DatabaseSelectObject();
        $element->setAdapter($container->get('model-primary-adapter'));
        return $element;
    }
}