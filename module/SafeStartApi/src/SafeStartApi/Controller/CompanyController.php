<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class CompanyController extends RestrictedAccessRestController
{
    public function getVehiclesAction()
    {
        //todo: check access to company
        /*
            todo: add json schema
            if (!$this->_requestIsValid('admin/getcompanies')) return $this->_showBadRequest();
        */
        $companyId = (int)$this->getRequest()->getQuery('companyId');
        $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);

        if (!$company) {
            $this->answer = array(
                "errorMessage" => "Company not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404, 404);
        }

        $this->answer = array(

        );

        $query = $this->em->createQuery('SELECT v FROM SafeStartApi\Entity\Vehicle v WHERE v.deleted = 0 AND v.company = ?1');
        $query->setParameter(1, $company);
        $items = $query->getResult();

        foreach ($items as $item) {
            $this->answer[] = $item->toMenuArray();
        }

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateVehicleAction()
    {

        $this->answer = array(
            'done' => true,
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

    public function deleteVehicleAction()
    {
        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getUsersAction()
    {
        $companyId = (int)$this->getRequest()->getQuery('companyId');
        $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);

        if (!$company) {
            $this->answer = array(
                "errorMessage" => "Company not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404, 404);
        }

        $this->answer = array();

        $query = $this->em->createQuery('SELECT u FROM SafeStartApi\Entity\User u WHERE u.deleted = 0 AND u.company = ?1');
        $query->setParameter(1, $company);
        $items = $query->getResult();

        foreach ($items as $item) {
            $this->answer[] = $item->toArray();
        }

        return $this->AnswerPlugin()->format($this->answer);

    }
}
