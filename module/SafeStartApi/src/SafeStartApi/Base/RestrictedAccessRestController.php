<?php
namespace SafeStartApi\Base;

use SafeStartApi\Base\Exception\Rest401;

class RestrictedAccessRestController extends RestController
{
    public function onDispatchEvent()
    {
        parent::onDispatchEvent();
        if (!$this->authService->hasIdentity()) throw new Rest401('Access denied');
    }
}
