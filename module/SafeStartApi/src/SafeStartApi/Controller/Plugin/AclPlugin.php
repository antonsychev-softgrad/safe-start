<?php

namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;

class AclPlugin extends AbstractPlugin
{
    protected $acl;

    public function __construct()
    {
        $this->acl = new Acl();

        $roleGuest = new Role('guest');
        $this->acl->addRole($roleGuest);
        $this->acl->addRole(new Role('user'), $roleGuest);
        $this->acl->addRole(new Role('companyAdmin'), 'user');
        $this->acl->addRole(new Role('superAdmin'), 'companyAdmin');

        $this->acl->allow($roleGuest, null, 'view');
    }

    public function isAllowed($role = null, $resource = null)
    {
        if(!is_null($role)) {
            return $this->acl->isAllowed($role, $resource);
        }
        return false;
    }
}