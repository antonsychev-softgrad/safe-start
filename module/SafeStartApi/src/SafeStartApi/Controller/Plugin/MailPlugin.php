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

        $view = new \Zend\View\Renderer\PhpRenderer();
        $resolver = new \Zend\View\Resolver\TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => 'email/' . $template,
        ));
        $view->setResolver($resolver);
        $viewModel = new \Zend\View\Model\ViewModel();
        $viewModel
            ->setTemplate('mailTemplate')
            ->setVariables($params);

        $message = new Message();
        $transport = $this->getController()->getServiceLocator()->get('mail.transport');

        $message
            ->setSubject($subject)
            ->setBody($view->render($viewModel))
            ->setFrom($moduleConfig['mail']['from'])
            ->setTo($to);
        $transport->send($message);
    }
}