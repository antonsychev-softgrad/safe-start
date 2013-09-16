<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class MailPlugin extends AbstractPlugin
{
    protected $viewModel;

    public function send($subject, $to, $template, $params = array(), $pdfFileName = '')
    {
        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');

        $viewModel = new ViewModel($params);
        $viewModel->setTemplate('mail/' . $template);

        $html = $this->getController()->getServiceLocator()
            ->get('viewrenderer')
            ->render($viewModel);

        $message = new Message();
        $transport = $this->getController()->getServiceLocator()->get('mail.transport');

        if(!empty($pdfFileName)) {
            $html = new MimeMessage;
            $fileContent = file_get_contents($pdfFileName);
            $attachment = new MimePart($fileContent);
            $attachment->type = 'application/x-pdf';
            $bodyMessage = new MimePart($html);
            $bodyMessage->type = 'text/html';
            $html->setParts(array($bodyMessage, $attachment));
        }

        $message
            ->setSubject($subject)
            ->setBody($html)
            ->setFrom($moduleConfig['mail']['from'])
            ->setTo($to);
        $transport->send($message);
    }
}