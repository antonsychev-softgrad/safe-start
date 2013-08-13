<?php
namespace SafeStartApi\Base;

use SafeStartApi\Base\Exception\Rest403;

class AdminAccessRestController extends RestrictedAccessRestController
{
    public function onDispatchEvent()
    {
        parent::onDispatchEvent();
        if($this->AclPlugin()->isAllowed('adminPanel', 'view')) throw new Rest403('Access denied');
    }
}
