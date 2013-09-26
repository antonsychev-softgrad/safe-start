<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime as Mime;

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

        if(!empty($pdfFileName) && file_exists($pdfFileName)) {

            $content  = new MimeMessage();
            $htmlPart = new MimePart($html);
            $htmlPart->type = 'text/html';
            $content->setParts(array($htmlPart));

            $contentPart = new MimePart($content->generateMessage());
            $contentPart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' . $content->getMime()->boundary() . '"';
            $bodyParts = array($contentPart);

            $attachment = new MimePart(fopen($pdfFileName, 'r'));
            $attachment->type = 'application/pdf';
            $attachment->encoding    = Mime::ENCODING_BASE64;
            $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
            $attachment->filename = basename($pdfFileName);
            $bodyParts[] = $attachment;

            $body = new MimeMessage();
            $body->setParts($bodyParts);
        } else {
            $body = $html;
        }

        $message->setEncoding('utf-8')
            ->setSubject($subject)
            ->setFrom($moduleConfig['mail']['from'])
            ->setTo($to)
            ->setBody($body);

        $transport->send($message);
    }
}