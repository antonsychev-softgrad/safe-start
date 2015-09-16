<?php

namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Validator\EmailAddress as EmailValidator;

class ValidationPlugin extends AbstractPlugin
{
    protected $emailValidator;

    public function __construct()
    {
        $this->emailValidator = new EmailValidator();
    }

    public function isEmailExists($mail = null)
    {
        if(!is_null($mail) && $this->emailValidator->isValid($mail)) {

            $userRep = $this->getController()->em->getRepository('SafeStartApi\Entity\User');
            $user = $userRep->findOneByEmail($mail);

            if($user) {
                return true;
            }
        }
        return false;
    }

    public function isValidEmail($mail = null)
    {
        if(!is_null($mail) && $this->emailValidator->isValid($mail)) {
            return true;
        }
        return false;
    }
}