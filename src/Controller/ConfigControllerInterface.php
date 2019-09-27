<?php 
namespace Midnet\Controller;

interface ConfigControllerInterface
{
    public function createDatabase();
    
    public function clearDatabase();
    
    public function setRoute();
}