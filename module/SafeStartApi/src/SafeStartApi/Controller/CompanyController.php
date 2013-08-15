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
        $this->answer = array();

        /*  $query = $this->em->createQuery('SELECT c FROM SafeStartApi\Entity\Company c WHERE c.deleted = 0');
          $items = $query->getResult();

          foreach ($items as $item) {
              $this->answer[] = $item->toArray();
          }*/

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
        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
