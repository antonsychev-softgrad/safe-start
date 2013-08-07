<?php

namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AclPlugin extends AbstractPlugin
{
    protected $acl;

    public function __construct()
    {
        $this->acl = new Acl();

        $this->setRoles();
        $this->setResource();

        $this->setAccessList();
    }

    private function setRoles()
    {
        $this->acl->addRole(new Role('guest'));
        $this->acl->addRole(new Role('user'), 'guest');
        $this->acl->addRole(new Role('companyAdmin'), 'user');
        $this->acl->addRole(new Role('superAdmin'), 'companyAdmin');
    }

    private function setResource()
    {
        $this->acl->addResource(new Resource('adminPanel'));
        $this->acl->addResource(new Resource('someResource'));
    }

    private function setAccessList()
    {
        $this->setAccess(array(
            'guest' => array(
                array(
                    'resource' => 'adminPanel',
                    'action' => 'view',
                    'access' => 'allow'
                ),
                array(
                    'resource' => 'someResource',
                    'action' => 'view',
                    'access' => 'deny'
                ),
            ),
            'user' => array(
                array(
                    'resource' => 'adminPanel',
                    'action' => 'view',
                    'access' => 'allow'
                ),
            ),
        ));
    }

    private function setAccess($rolesList = array())
    {
        foreach($rolesList as $role => $paramsArray) {
            foreach($paramsArray as $params) {
                $this->acl->{$params['access']}($role, $params['resource'], $params['action']);
            }
        }
    }

    public function isAllowed($resource = null, $privilege = null)
    {
        if($this->getController()->authService->hasIdentity()) {
            $user = $this->authService->getStorage()->read();
            $role = $user('role');
        } else {
            $role = 'guest';
        }
        return $this->acl->isAllowed($role, $resource, $privilege);
    }
}