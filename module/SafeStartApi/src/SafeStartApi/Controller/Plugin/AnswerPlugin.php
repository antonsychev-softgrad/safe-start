<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;

class AnswerPlugin extends AbstractPlugin
{
    protected $viewModel;

    public function format($answer, $status='200', $type='json')
    {
        $this->viewModel = new ViewModel;
        $this->viewModel->setTemplate($type . '/' . $status);
        $this->viewModel->setTerminal(true);
        $this->viewModel->setVariable('answer', $answer);
        return $this->viewModel;
    }
}