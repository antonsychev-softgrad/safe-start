<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\AdminAccessRestController;

class AdminController extends AdminAccessRestController
{
    public function getCompaniesAction()
    {
        /*
            todo: add json schema
            if (!$this->_requestIsValid('admin/getcompanies')) return $this->_showBadRequest();
        */
        $this->answer = array();

        $query = $this->em->createQuery('SELECT c FROM SafeStartApi\Entity\Company c WHERE c.deleted = 0');
        $items = $query->getResult();

        foreach ($items as $item) {
            $this->answer[] = $item->toArray();
        }

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateCompanyAction()
    {
      //  if (!$this->_requestIsValid('admin/updatecompany')) return $this->_showBadRequest();

        $companyId = (int)$this->params('id');
        if ($companyId) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404, 404);
            }
        } else {
            $company = new \SafeStartApi\Entity\Company();
        }

        // set company data
        $company->setTitle($this->data->title);
        $company->setAddress($this->data->address);
        $company->setPhone($this->data->phone);
        $company->setDescription($this->data->description);
        $company->setRestricted((bool)$this->data->restricted);
        $company->setMaxUsers($this->data->restricted ? (int)$this->data->max_users : 0);
        $company->setMaxVehicles($this->data->restricted ? (int)$this->data->max_vehicles : 0);
        if ($this->data->restricted) {
            $expiryDate = new \DateTime();
            $expiryDate->setTimestamp((int)$this->data->expiry_date);
            $company->setExpiryDate($expiryDate);
        }
        $this->em->persist($company);
        // set company admin
        $userRep = $this->em->getRepository('SafeStartApi\Entity\User');
        $user = $userRep->findOneBy(array('email' => $this->data->email));

        if (!$user) {
            $user = new \SafeStartApi\Entity\User();
            $user->setEmail($this->data->email);
            $user->setFirstName($this->data->firstName);
            $user->setUsername($this->data->firstName);
            $user->setRole('companyAdmin');
            $this->em->persist($user);
        }

        $company->setAdmin($user);

        $this->em->flush();

        $this->answer = array(
            'done' => true,
            'companyId' => $company->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

    public function sendCredentialsAction()
    {
     //   if (!$this->_requestIsValid('admin/sendcredentials')) return $this->_showBadRequest();

        $companyId = (int)$this->params('id');

        if ($companyId) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404, 404);
            }
        } else {
            $this->_showBadRequest();
        }

        $user = $company->getAdmin();
        $password = md5($user->getId() . time() . rand());
        $user->setPlainPassword($password);
        $this->em->flush();

        $this->MailPlugin()->send(
            'Credentials',
            $user->getEmail(),
            'creds.phtml',
            array(
                'username' => $user->getUsername(),
                'password' => $password,
            )
        );

        $this->answer = array(
            'done' => true,
            'companyId' => $company->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function deleteCompanyAction()
    {
        $companyId = (int)$this->params('id');
        if ($companyId) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404, 404);
            }
        }
        $company->setDeleted(1);
        $this->em->flush();

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
