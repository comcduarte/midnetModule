<?php 
namespace Midnet\Controller;

use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

abstract class AbstractConfigController extends AbstractActionController implements ConfigControllerInterface
{
    use AdapterAwareTrait;
    
    private $route;
    
    public function indexAction()
    {
        $view = new ViewModel();
        $view->setTemplate('config');
        $view->setVariables([
            'route' => $this->getRoute(),
        ]);
        return ($view);
    }
    
    public function clearAction()
    {
        $this->clearDatabase();
        $this->flashMessenger()->addSuccessMessage("Database tables cleared.");
        return $this->redirect()->toRoute('ballots/config');
    }
    
    public function createAction()
    {
        $this->createDatabase();
        $this->flashMessenger()->addSuccessMessage("Database tables populated.");
        return $this->redirect()->toRoute('ballots/config');
    }
    
    public function getRoute()
    {
        return $this->route;
    }
    
    public function initialize()
    {
        $this->setRoute();
    }
}