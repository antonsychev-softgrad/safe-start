<?php
namespace SafeStartApi\Base;

use SafeStartApi\Base\Exception\Rest401;
use SafeStartApi\Base\Exception\Rest403;

class RestrictedAccessRestController extends RestController
{
    public function onDispatchEvent()
    {
        parent::onDispatchEvent();
        if (!$this->authService->hasIdentity()) throw new Rest401('Access denied');
        if ($this->_checkExpiryDate()) throw new Rest403('You company subscription expired');
    }
}
