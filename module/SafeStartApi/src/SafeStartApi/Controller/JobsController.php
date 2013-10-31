<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\MvcEvent;

class JobsController extends AbstractActionController
{
    protected $console;
    protected $logger;
    public $em;
    public $moduleConfig;

    public function onDispatch(MvcEvent $e)
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }
        // todo: find better way for global access
        \SafeStartApi\Application::setCurrentControllerServiceLocator($this->getServiceLocator());
        $this->logger = $this->getServiceLocator()->get('ResqueLogger');
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
        $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        parent::onDispatch($e);
    }

    public function processNewDbCheckListAction()
    {
        $request = $this->getRequest();
        $checkListId = $request->getParam('checkListId');
        $this->logger->info("Run Process New Db CheckList Action with checkListId = $checkListId \r\n");
        $checkList = $this->em->find('SafeStartApi\Entity\CheckList', $checkListId);
        if (!$checkList) {
            $this->logger->info("CheckList with checkListId = $checkListId not found \r\n");
            return "CheckList with checkListId = $checkListId not found \r\n";
        }
        $this->processChecklistPlugin()->pushNewChecklistNotification($checkList);
        $this->processChecklistPlugin()->setInspectionStatistic($checkList);
        $this->logger->info("Success Process New Db CheckList Action with checkListId = $checkListId \r\n");
    }

    public function processNewEmailCheckListAction()
    {
        $request = $this->getRequest();
        $checkListId = $request->getParam('checkListId');
        $this->logger->info("Run Process New Email CheckList Action with checkListId = $checkListId \r\n");
        $checkList = $this->em->find('SafeStartApi\Entity\CheckList', $checkListId);
        if (!$checkList) {
            $this->logger->info("CheckList with checkListId = $checkListId not found \r\n");
            return "CheckList with checkListId = $checkListId not found \r\n";
        }
        $emails = array();
        $emailsString = $request->getParam('emails');
        if (empty($emailsString)) {
            $this->logger->info("No emails for send to \r\n");
            return 'No emails for send to';
        }
        $emailsStringArray = explode(',', $emailsString);
        foreach ($emailsStringArray as $emailsStringArrayItem) {
            $emailsStringArrayItem = explode(':', $emailsStringArrayItem);
            $emails[] = array(
                'email' => $emailsStringArrayItem[0],
                'name' => isset($emailsStringArrayItem[1]) ? $emailsStringArrayItem[1] : 'friend',
            );
        }

        $pdf = $this->inspectionPdf()->create($checkList);
        $this->processChecklistPlugin()->setInspectionStatistic($checkList);
        if (file_exists($pdf)) {
            foreach ($emails as $email) {
                if (empty($email)) continue;
                $email = (array)$email;
                $this->logger->info("Send email to ".$email['email']."\r\n");
                $this->MailPlugin()->send(
                    $this->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                    $email['email'],
                    'checklist.phtml',
                    array(
                        'name' => $email['name']
                    ),
                    $pdf
                );
            }
        }
        $this->logger->info("Success Process New Email CheckList Action with checkListId = $checkListId \r\n");
    }

    public function processCheckListResend()
    {
        $request = $this->getRequest();
        $checkListId = $request->getParam('checkListId');
        $this->logger->info("Run Process CheckList Re-send Action with checkListId = $checkListId \r\n");
        $checkList = $this->em->find('SafeStartApi\Entity\CheckList', $checkListId);
        if (!$checkList) {
            $this->logger->info("CheckList with checkListId = $checkListId not found \r\n");
            return "CheckList with checkListId = $checkListId not found \r\n";
        }
        $emails = array();
        $emailsString = $request->getParam('emails');
        if (empty($emailsString)) {
            $this->logger->info("No emails for send to \r\n");
            return 'No emails for send to';
        }
        $emailsStringArray = explode(',', $emailsString);
        foreach ($emailsStringArray as $emailsStringArrayItem) {
            $emailsStringArrayItem = explode(':', $emailsStringArrayItem);
            $emails[] = array(
                'email' => $emailsStringArrayItem[0],
                'name' => isset($emailsStringArrayItem[1]) ? $emailsStringArrayItem[1] : 'friend',
            );
        }

        $link = $checkList->getPdfLink();
        $cache = \SafeStartApi\Application::getCache();
        $cashKey = $link;
        $path = '';
        if ($cashKey && $cache->hasItem($cashKey)) {
            $path = $this->inspectionPdf()->getFilePathByName($link);
        }
        if (!$link || !file_exists($path)) $path = $this->inspectionPdf()->create($checkList);
        if (file_exists($path)) {
            foreach($emails as $email) {
                $email = (array) $email;
                $this->MailPlugin()->send(
                    $this->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                    $email['email'],
                    'checklist.phtml',
                    array(
                        'name' => isset($email['name']) ? $email['name'] : 'friend'
                    ),
                    $path
                );
            }
            return $this->AnswerPlugin()->format($this->answer);
        } else {
            $this->logger->info("PDF document was not generated");
        }

        $this->logger->info("Success Process CheckList Re-send Action with checkListId = $checkListId \r\n");
    }

    public function pingEmailAction()
    {
        $this->MailPlugin()->send(
            'New ping email',
            'ponomarenko.t@gmail.com',
            'test.phtml',
            array(
                'name' => 'Test User'
            )
        );
    }
}
