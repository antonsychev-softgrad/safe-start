<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;

class MailPlugin extends AbstractPlugin
{
    protected $viewModel;

    public function send($subject, $to, $template, $params = array())
    {
        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');

        $viewModel = new ViewModel($params);
        $viewModel->setTemplate('mail/' . $template);

        $html = $this->getController()->getServiceLocator()
            ->get('viewrenderer')
            ->render($viewModel);

        $message = new Message();
        $transport = $this->getController()->getServiceLocator()->get('mail.transport');

        $message
            ->setSubject($subject)
            ->setBody($html)
            ->setFrom($moduleConfig['mail']['from'])
            ->setTo($to);
        $transport->send($message);
    }
}