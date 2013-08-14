<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\AdminAccessRestController;

class AdminController extends AdminAccessRestController
{
    public function getCompaniesAction() {

        $this->answer = array(
            array(
                'id' => 1,
                'title' => 'Company 1'
            ),
            array(
                'id' => 2,
                'title' => 'Company 2'
            ),
            array(
                'id' => 3,
                'title' => 'Company 3',
            )
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
