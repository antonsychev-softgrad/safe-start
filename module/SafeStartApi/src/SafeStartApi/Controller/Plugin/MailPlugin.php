<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime as Mime;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;

class MailPlugin extends AbstractPlugin
{
    protected $viewModel;

    public function send($subject, $to, $template, $params = array(), $pdfFileName = '')
    {
        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');

        $viewModel = new ViewModel($params);
        $viewModel->setTemplate('mail/' . $template);
        $renderer = new PhpRenderer();
        $resolver = new Resolver\AggregateResolver();
        $renderer->setResolver($resolver);
        $viewsDir = __DIR__ . '/../../../../view/';
        /* $map = new Resolver\TemplateMapResolver(array(
             'layout'      => __DIR__ . '/view/layout.phtml',
             'index/index' => __DIR__ . '/view/index/index.phtml',
         ));*/
        $stack = new Resolver\TemplatePathStack(array(
            'script_paths' => array(
                $viewsDir
            )
        ));
        $resolver->attach($stack);
        $html = $renderer->render($viewModel);
        $message = new Message();
        $transport = $this->getController()->getServiceLocator()->get('mail.transport');

        if (!empty($pdfFileName) && file_exists($pdfFileName)) {
            $content = new MimeMessage();
            $htmlPart = new MimePart($html);
            $htmlPart->type = 'text/html';
            $content->setParts(array($htmlPart));

            $contentPart = new MimePart($content->generateMessage());
            $contentPart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' . $content->getMime()->boundary() . '"';
            $bodyParts = array($contentPart);

            $attachment = new MimePart(fopen($pdfFileName, 'r'));
            $attachment->type = 'application/pdf';
            $attachment->encoding = Mime::ENCODING_BASE64;
            $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
            $attachment->filename = basename($pdfFileName);
            $bodyParts[] = $attachment;

            $body = new MimeMessage();
            $body->setParts($bodyParts);
        } else {
            $body = new MimeMessage();
            $htmlPart = new MimePart($html);
            $htmlPart->type = 'text/html';
            $body->setParts(array($htmlPart));
        }

        $message->setEncoding('utf-8')
            ->setSubject($subject)
            ->setFrom($moduleConfig['mail']['from'])
            ->setTo($to)
            ->setBody($body);

        $transport->send($message);
    }
}