<?php
namespace SafeStartApi\Base;

use SafeStartApi\Base\Exception\Rest403;
use SafeStartApi\Base\RestrictedAccessRestController;

class AdminAccessRestController extends RestrictedAccessRestController
{
    public function onDispatchEvent()
    {
        parent::onDispatchEvent();
        if(!$this->AclPlugin()->isAllowed('adminPanel', 'superAccess')) throw new Rest403('Access denied');
    }
}
