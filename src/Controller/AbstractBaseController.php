<?php 
namespace Midnet\Controller;

use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Db\Sql\Where;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\View\Model\ViewModel;

class AbstractBaseController extends AbstractActionController
{
    use AdapterAwareTrait;
    
    public $model;
    public $form;
    
    public function indexAction()
    {
        $view = new ViewModel();
        
        $records = $this->model->fetchAll(new Where());
        
        if (is_array($records)) {
            $paginator = new Paginator(new ArrayAdapter($records));
            $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 1));
            
            $count = $this->params()->fromRoute('count', 15);
            $paginator->setItemCountPerPage($count);
        } else {
            $records = [];
            $count = 0;
        }
        
        $header = [];
        if (!empty($records)) {
            $header = array_keys($records[0]); 
        }
        
        $view->setvariables ([
            'data' => $records,
            'header' => $header,
            'count' => $count,
            'primary_key' => $this->model->getPrimaryKey(),
        ]);
        return $view;
    }
    
    public function createAction()
    {
        $view = new ViewModel();
        
        $request = $this->getRequest();
        $this->form->bind($this->model);
        
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            
            $this->form->setData($post);
            
            if ($this->form->isValid()) {
                $user = $this->currentUser();
                $this->model->setCurrentUser($user->USERNAME);
                $this->model->create();
                
                $this->flashmessenger()->addSuccessMessage('Add New Record Successful');
            } else {
                $this->flashmessenger()->addErrorMessage("Form is Invalid.");
            }
            
            $url = $this->getRequest()->getHeader('Referer')->getUri();
            return $this->redirect()->toUrl($url);
        }
        
        $view->setVariables([
            'form' => $this->form,
            'title' => 'Add New Record',
        ]);
        
        return ($view);
    }
    
    public function updateAction()
    {
        $primary_key = $this->params()->fromRoute(strtolower($this->model->getPrimaryKey()),0);
        if (!$primary_key) {
            $this->flashmessenger()->addErrorMessage("Unable to retrieve record. Value not passed.");
            
            $url = $this->getRequest()->getHeader('Referer')->getUri();
            return $this->redirect()->toUrl($url);
        }
        
        $view = new ViewModel();
        
        $this->model->read([$this->model->getPrimaryKey() => $primary_key]);
        
        $this->form->bind($this->model);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            $this->form->setData($data);
            
            if ($this->form->isValid()) {
                $user = $this->currentUser();
                $this->model->setCurrentUser($user->USERNAME);
                $this->model->update();
                
                $this->flashmessenger()->addSuccessMessage('Update Successful');
                
                $url = $this->getRequest()->getHeader('Referer')->getUri();
                return $this->redirect()->toUrl($url);
            }
            $this->flashmessenger()->addErrorMessage("Form submission was invalid.");
        }
        
        $view->setVariables([
            'form' => $this->form,
            'title' => 'Update Record',
            'primary_key' => $this->model->getPrimaryKey(),
        ]);
        
        return ($view);
    }
    
    public function deleteAction()
    {
        $primary_key = $this->getPrimaryKey();
        $this->model->read([$this->model->getPrimaryKey() => $primary_key]);
        $user = $this->currentUser();
        $this->model->setCurrentUser($user->USERNAME);
        $this->model->delete();
        
        $this->flashmessenger()->addSuccessMessage("Record Deleted.");
        
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
    
    public function getModel()
    {
        return $this->model;
    }
    
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }
    
    public function getForm()
    {
        return $this->form;
    }
    
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }
    
    private function getPrimaryKey()
    {
        $primary_key = $this->params()->fromRoute(strtolower($this->model->getPrimaryKey()),0);
        if (!$primary_key) {
            $this->flashmessenger()->addErrorMessage("Unable to retrieve record. Value not passed.");
            
            $url = $this->getRequest()->getHeader('Referer')->getUri();
            return $this->redirect()->toUrl($url);
        }
        return $primary_key;
    }
}
