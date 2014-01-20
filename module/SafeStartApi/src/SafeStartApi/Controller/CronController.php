<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class CronController extends AbstractActionController
{

    // public function __construct()
    // {
        // $request = $this->getRequest();

        // if (!$request instanceof ConsoleRequest) {

        //     throw new \RuntimeException(get_class($request));
        // }
        // todo: find better way for global access
        // \SafeStartApi\Application::setCurrentControllerServiceLocator($this->getServiceLocator());
        // $this->logger = $this->getServiceLocator()->get('ResqueLogger');
        // $this->moduleConfig = $this->getServiceLocator()->get('Config');
    // }

    public function indexAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Check if the user used --verbose or -v flag
        $verbose = $request->getParam('verbose');

        if (!$verbose){
            return "Test 1";
        }else{
            return "Test 2";
        }

    }

    public function processSubExpiryEmailNotifyAction() 
    {
        $request = $this->getRequest();
        $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $companyRep = $this->em->getRepository('SafeStartApi\Entity\Company');

        $companies = $companyRep->findBy(array('restricted' => true));

        $dayInSec = 24 * 60 * 60;

        $nextWeek = strtotime("midnight", time() + 8 * $dayInSec);
        $nextTwoWeeks = $nextWeek + 7 * $dayInSec;
        $nextFourWeeks = $nextTwoWeeks + 14 * $dayInSec;

        $testString = "Companies: \n";
        $expiryDate = 0;
        foreach ($companies as $company) {
            $expiryDate = $company->getExpiryDate();
            $testString .= "\t" . $company->getTitle();
            if ($expiryDate >= $nextWeek && $expiryDate < ($nextWeek + $dayInSec - 1)) {
                // companies that expires at 7 days
                $testString .= ' <- This company expires at 7 days;';
            }
            if ($expiryDate >= $nextTwoWeeks && $expiryDate < ($nextTwoWeeks + $dayInSec - 1)) {
                // companies that expires at 14 days
                $testString .= ' <- This company expires at 14 days;';
            }
            if ($expiryDate >= $nextFourWeeks && $expiryDate < ($nextFourWeeks + $dayInSec - 1)) {
                // companies that expires at 28 days
                $testString .= ' <- This company expires at 28 days;';
            }
            $testString .= "\n";
        }


        return $testString . "\n";

        $this->logger->info("Run Process Subscription Expiry Email Notification \r\n");
    }

    public function processSubExpiryPushNotifyAction() 
    {
        $request = $this->getRequest();
        $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $companyRep = $this->em->getRepository('SafeStartApi\Entity\Company');

        $companies = $companyRep->findBy(array('restricted' => true));

        $dayInSec = 24 * 60 * 60;

        $nextDay = strtotime("midnight", time() + $dayInSec);
        $nextWeek = $nextDay + 7 * $dayInSec;

        $testString = "Companies: \n";
        $expiryDate = 0;
        foreach ($companies as $company) {
            $expiryDate = $company->getExpiryDate();
            $testString .= "\t" . $company->getTitle();
            if ($expiryDate >= $nextDay && $expiryDate < ($nextDay + $dayInSec - 1)) {
                $testString .= ' <- This company expires at 1 days;';
            }
            if ($expiryDate >= $nextWeek && $expiryDate < ($nextWeek + $dayInSec - 1)) {
                // companies that expires at 7 days
                $testString .= ' <- This company expires at 7 days;';
            }
            $testString .= "\n";
        }
        return $testString . "\n";

        $this->logger->info("Run Process Subscription Expiry Email Notification \r\n");
    }

}
