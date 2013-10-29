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
        if (empty($emailsString)) return 'Mo emails for send to';
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
                $this->MailPlugin()->send(
                    'New inspection report',
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
