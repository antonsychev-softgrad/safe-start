<?php

namespace SafeStartApi\Controller;

use Doctrine\Common\Collections\ArrayCollection;
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

            $checkAccess = function($email = '') {
                $qb = $this->em->createQueryBuilder();
                $expr = $qb->expr();
                $qb->select($expr->count('e.id'))
                    ->from('SafeStartApi\Entity\User', 'e')
                    ->where(
                        $expr->andX(
                            $expr->like('e.email', $expr->literal($email)),
                            $expr->isNotNull('e.password'),
                            $expr->eq('e.deleted', $expr->literal(0))
                        )
                    );
                $qb->setMaxResults(1);
                $qb->setFirstResult(0);
                return (boolean) $qb->getQuery()->getSingleScalarResult();
            };

            foreach ($emails as $email) {
                if (empty($email)) continue;
                $email = (array)$email;
                $this->logger->info("Send email to ".$email['email']."\r\n");
                $this->MailPlugin()->send(
                    $this->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                    $email['email'],
                    'checklist.phtml',
                    array(
                        'name' => $email['name'],
                        'plantId' => $checkList->getVehicle() ? $checkList->getVehicle()->getPlantId() : '-',
                        'uploadedByName' => $checkList->getOperatorName(),
                        'siteUrl' => $this->moduleConfig['params']['site_url'],
                        'emailStaticContentUrl' => $this->moduleConfig['params']['email_static_content_url'],
                        'showLoginUrl' => $checkAccess($email['email']),
                    ),
                    $pdf
                );
            }
        }
        $this->logger->info("Success Process New Email CheckList Action with checkListId = $checkListId \r\n");
    }

    public function processCheckListResendAction()
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

            $checkAccess = function($email) {
                $qb = $this->em->createQueryBuilder();
                $expr = $qb->expr();
                $qb->select($expr->count('e.id'))
                    ->from('SafeStartApi\Entity\User', 'e')
                    ->where(
                        $expr->andX(
                            $expr->like('e.email', $expr->literal($email)),
                            $expr->isNotNull('e.password'),
                            $expr->eq('e.deleted', $expr->literal(0))
                        )
                    );
                $qb->setMaxResults(1);
                $qb->setFirstResult(0);
                return (boolean) $qb->getQuery()->getSingleScalarResult();
            };

            foreach($emails as $email) {
                if (empty($email)) continue;
                $email = (array) $email;
                $this->MailPlugin()->send(
                    $this->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                    $email['email'],
                    'checklist.phtml',
                    array(
                        'name' => isset($email['name']) ? $email['name'] : 'friend',
                        'plantId' => $checkList->getVehicle() ? $checkList->getVehicle()->getPlantId() : '-',
                        'uploadedByName' => $checkList->getOperatorName(),
                        'siteUrl' => $this->moduleConfig['params']['site_url'],
                        'emailStaticContentUrl' => $this->moduleConfig['params']['email_static_content_url'],
                        'showLoginUrl' => $checkAccess($email['email'])
                    ),
                    $path
                );
            }
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





    public function processCheckCompanyPaymentsAction()
    {
        $this->logger->info("Run check payments of companies");

        $em   = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $qb   = $em->createQueryBuilder();
        $expr = $qb->expr();

        $timezone    = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        $nowDateTime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), $timezone)->setTimezone($timezone);
        $last6Mnts   = clone $nowDateTime;
        $last6Mnts->sub(new \DateInterval("P6M"));

        $_qb = clone $qb;
        $_qb->select('e')
            ->from('SafeStartApi\Entity\Company', 'e')
            ->where(
                $expr->andX(
                    $expr->isNotNull('e.payment_date'),
                    $expr->isNotNull('e.expiryDate'),
                    $expr->not(
                        $expr->between('e.payment_date', "'" . $last6Mnts->format('Y-m-d H:i:s') . "'", "'" . $nowDateTime->format('Y-m-d H:i:s') . "'")),
                    $expr->lte('e.expiryDate', "'" . $last6Mnts->format('Y-m-d H:i:s') . "'")
                )
            )
            ->andWhere($expr->eq('e.deleted', 0));
        $companies = $_qb->getQuery()->getResult();
        foreach ($companies as $company) {
            $vehicles = $company->getVehicles();
            foreach ($vehicles as $vehicle) {
                $users            = $vehicle->getUsers();
                $responsibleUsers = $vehicle->getResponsibleUsers();
                $users            = new ArrayCollection(array_merge($users->toArray(), $responsibleUsers->toArray()));
                $cache            = \SafeStartApi\Application::getCache();
                foreach ($users as $user) {
                    $cashKey = "getUserVehiclesList" . $user->getId();
                    $cache->removeItem($cashKey);
                }
                $vehicle->setPlantId(time() . " " . $vehicle->getPlantId());
                $vehicle->setDeleted(1);
            }

            $admin = $company->getAdmin();
            if ($admin !== null) {
                $uName   = $admin->getUsername();
                $uEmail  = $admin->getEmail();
                $uAsUniq = hash("sha1", $uEmail . microtime(true));
                $admin->setUsername($uName . '__#' . $uAsUniq);
                $admin->setEmail($uEmail . '__#' . $uAsUniq);
                $admin->setEnabled(0);
                $admin->setDeleted(1);
            }
            $company->setDeleted(1);

            $msg = sprintf("Change status on \"deleted\" for the company %s", $company->getTitle());
            $this->logger->info($msg);

            $em->flush();
        }

        $this->logger->info("Success check payments of companies");
    }

    public function processSyncThirdPartyDbAction()
    {
        $this->logger->info("Run sync payments of companies");

        $moduleConfig     = $this->moduleConfig['3rdParty'];
        $dbPrefix         = $moduleConfig['dbPrefix'];
        $availableTypesId = $moduleConfig['availableTypes'];
        $connectionParams = $moduleConfig['connectionParams'];

        $em   = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $qb   = $em->createQueryBuilder();
        $expr = $qb->expr();

        $config     = new \Doctrine\DBAL\Configuration();
        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        try {
            $connection->connect();
        } catch(\Exception $e) {
            // check db connection params and try later.
            $msg = "Error with message: ".$e->getMessage().".\r\nPlease check db connection params and try later.";
            $this->logger->info($msg);
            return $msg ."\r\n";
        }

        $formatLabel = function ($label) {
            $label = preg_replace("/[^A-Za-z0-9]/is", "_", $label);
            $label = preg_replace("/[_]+/is", "_", $label);
            $label = strtolower($label);

            return $label;
        };

        $lastCompanyPaymentDate = null;
        $qb->select('e.payment_date')
            ->from('SafeStartApi\Entity\Company', 'e')
            ->where($expr->isNotNull('e.payment_date'))
            ->andWhere($expr->eq('e.deleted', 0))
            ->orderBy('e.payment_date', 'DESC');
        $query = $qb->getQuery();
        $query->setMaxResults(1);
        try {
            $lastCompanyPaymentDate = $query->getSingleScalarResult();
        } catch (\Exception $e) {
            // \Doctrine\ORM\NoResultException
        }

        if (null !== $lastCompanyPaymentDate && $lastCompanyPaymentDate !== "") {
            if (!preg_match("/^([0-9]{4})[-]([0][1-9]|[1][0-2])[-]([0][1-9]|[1|2][0-9]|[3][0|1])\s(([0-1][0-9])|([2][0-3])):([0-5][0-9]):([0-5][0-9])$/is", $lastCompanyPaymentDate, $matches)) {
                // format
                $lastCompanyPaymentDate = null;
            }
        }

        foreach ($availableTypesId as $availableTypeK => $formId) {
            $sql      = "SELECT display_meta FROM {$dbPrefix}rg_form_meta WHERE form_id=:formId";
            $sqlPrms  = array(
                'formId' => $formId,
            );
            $formMeta = $connection->fetchAssoc($sql, $sqlPrms);
            if (!empty($formMeta)) {
                $form = json_decode($formMeta["display_meta"], true);
                if ($form === null) {
                    $form = @unserialize($formMeta["display_meta"]);
                }
                $formFields = array();
                if (is_array($form)) {
                    if (isset($form['fields']) && is_array($form['fields'])) {
                        foreach ($form['fields'] as $field) {
                            if (empty($field['label'])) {
                                continue;
                            }
                            $label                        = $formatLabel($field['label']);
                            $formFields["{$field['id']}"] = array('name' => $label);
                            if (isset($field['inputs']) && is_array($field['inputs'])) {
                                foreach ($field['inputs'] as $input) {
                                    if (empty($input['label'])) {
                                        continue;
                                    }
                                    $label                        = $formatLabel($input['label']);
                                    $formFields["{$input['id']}"] = array('name' => $label);
                                }
                            }
                        }
                    }
                }

                $sql     = "SELECT * FROM {$dbPrefix}rg_lead WHERE `form_id`=:formId";
                $sqlPrms = array(
                    'formId' => $formId,
                );
                if (null !== $lastCompanyPaymentDate) {
                    $sql .= " AND UNIX_TIMESTAMP(`date_created`) BETWEEN UNIX_TIMESTAMP(:paymentDate + INTERVAL 1 SECOND) AND UNIX_TIMESTAMP(:now)";
                    $sqlPrms['paymentDate'] = $lastCompanyPaymentDate;

                    $timezone    = new \DateTimeZone('UTC');
                    $nowDateTime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), $timezone)->setTimezone($timezone);
                    $sqlPrms['now'] = $nowDateTime->format('Y-m-d H:i:s');

                }

                $formLeads = $connection->fetchAll($sql, $sqlPrms);
                foreach ($formLeads as $counter => $formLead) {
                    $leadFormDetails = array();
                    $leadFormData    = array();
                    $leadDetails     = $connection->fetchAll(
                        $sql = "select field_number, `value` from {$dbPrefix}rg_lead_detail where form_id=:formId and lead_id=:leadId",
                        $sqlPrms = array(
                            'formId' => $formId,
                            'leadId' => $formLead['id']));
                    foreach ($leadDetails as $meta) {
                        if (preg_match_all("/(\d+)\./ix", "{$meta['field_number']}", $matches)) {
                            $matches = array_pop($matches);
                            $parent  = implode('.', $matches);
                            if (isset($formFields[$parent]['name'])) {
                                $key = $formFields[$parent]['name'] . '_' . $formFields["{$meta['field_number']}"]['name'];
                            } else {
                                $key = "{$meta['field_number']}";
                            }
                        } else {
                            $key = $formFields["{$meta['field_number']}"]['name'];
                        }
                        $leadFormData[$key]                     = $meta['value'];
                        $leadFormDetails[$meta['field_number']] = $meta['value'];
                    }

                    $formLead['_details']     = $leadFormDetails;
                    $formLead['_detailsData'] = $leadFormData;
                    $formLeads[$counter]      = $formLead;

                    $this->syncCompanyUserPayment($formLead, $availableTypeK);
                }

                //$this->logger->info(print_r($formLeads, true));
            }
        }
        $this->logger->info("Success sync payments of companies");
    }

    protected function syncCompanyUserPayment($leadData, $paymentType)
    {
        if (($leadData['payment_status'] !== 'Approved'
//            && $leadData['payment_date'] !== ''
//            && $leadData['payment_amount'] !== ''
//            && $leadData['transaction_id'] !== ''
            && $paymentType !== 'Free')
        ) {
            $msg = sprintf("Invalid payment status or payment type is not \"Free\" for payment with ID %s.", $leadData['id']);

            $this->logger->info($msg);
            return $msg;
        }

        $em   = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $qb   = $em->createQueryBuilder();
        $expr = $qb->expr();

        $company = null;
        $user    = null;

        $em->clear();

        if (isset($leadData['_detailsData']['email']) && $leadData['_detailsData']['email'] !== "") {
            $_qb = clone $qb;
            $_qb->select('e')
                ->from('SafeStartApi\Entity\User', 'e')
                ->where($expr->like('e.email', "'{$leadData['_detailsData']['email']}'"));
//            if(isset($leadData['_detailsData']['name_first']) && $leadData['_detailsData']['name_first'] !== "") {
//                $_qb->andWhere($expr->eq('e.first_name', "'{$leadData['_detailsData']['name_first']}'"));
//            }
//            if(isset($leadData['_detailsData']['last_name']) && $leadData['_detailsData']['last_name'] !== "") {
//                $_qb->andWhere($expr->eq('e.last_name', "'{$leadData['_detailsData']['name_last']}'"));
//            }

            $user = $_qb->getQuery()->getOneOrNullResult();
            if ($user === null) {
                $email     = $leadData['_detailsData']['email'];
                $firstName = isset($leadData['_detailsData']['name_first']) ? $leadData['_detailsData']['name_first'] : null;
                $lastName  = isset($leadData['_detailsData']['name_last']) ? $leadData['_detailsData']['name_last'] : null;
                if (empty($firstName) && empty($lastName)) {
                    $freeName = $lastName = isset($leadData['_detailsData']['name']) ? $leadData['_detailsData']['name'] : null;
                    if (!empty($freeName)) {
                        $freeName  = explode(' ', $freeName);
                        $firstName = $freeName[0];
                        unset($freeName[0]);
                        $lastName = implode(' ', $freeName);
                    }
                }

                $user = new \SafeStartApi\Entity\User();
                $user->setEmail($email);
                $user->setUsername($email);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setPosition(null);
                $user->setDepartment(null);
                $user->setRole('companyManager');
                $user->setEnabled(1);
                $user->setDeleted(0);
            }
        }

        $companyName = "";
        if (isset($leadData['_detailsData']['company_name']) && $leadData['_detailsData']['company_name'] !== "") {
            $companyName = $leadData['_detailsData']['company_name'];
        } else {
            $company = $user->getCompany();
            if (null !== $company) {
                $companyName = $company->getTitle();
            } else {
                $msg = sprintf("[Invalid data] Not found company name for payment with ID %s.", $leadData['id']);
                $this->logger->info($msg);
                return $msg;
            }
        }

        if (!empty($companyName)) {
            $_qb = clone $qb;
            $_qb->select('e')
                ->from('SafeStartApi\Entity\Company', 'e')
                ->where($expr->like($expr->lower('e.title'), "'" . strtolower($companyName) . "'"))
                ->andWhere($expr->eq('e.deleted', 0));
            $company = $_qb->getQuery()->getOneOrNullResult();
            if ($company === null) {
                $address = array();
                if (isset($leadData['_detailsData']['shipping_address_zip_postal_code']) && $leadData['_detailsData']['shipping_address_zip_postal_code'] !== "") {
                    $address[] = $leadData['_detailsData']['shipping_address_zip_postal_code'];
                }
                if (isset($leadData['_detailsData']['shipping_address_country']) && $leadData['_detailsData']['shipping_address_country'] !== "") {
                    $address[] = $leadData['_detailsData']['shipping_address_country'];
                }
                if (isset($leadData['_detailsData']['shipping_address_state_province']) && $leadData['_detailsData']['shipping_address_state_province'] !== "") {
                    $address[] = $leadData['_detailsData']['shipping_address_state_province'];
                }
                if (isset($leadData['_detailsData']['shipping_address_city']) && $leadData['_detailsData']['shipping_address_city'] !== "") {
                    $address[] = $leadData['_detailsData']['shipping_address_city'];
                }
                if (isset($leadData['_detailsData']['shipping_address_street_address']) && $leadData['_detailsData']['shipping_address_street_address'] !== "") {
                    $address[] = $leadData['_detailsData']['shipping_address_street_address'];
                }
                $address = implode(', ', $address);

                $company = new \SafeStartApi\Entity\Company();
                $company->setTitle($companyName);
                $company->setAddress($address);
                $company->setPhone(isset($leadData['_detailsData']['phone']) ? $leadData['_detailsData']['phone'] : null);
                $company->setDescription(null);
            } else {
                if (null !== $user && null === $user->getId()) {
                    $admin = $company->getAdmin();
                    if ($admin->getEmail() !== $user->getEmail()) {
                        $admin->setRole('companyManager');
                    }
                }
            }
        }

        if ($company !== null && $user !== null) {
            $timezone        = new \DateTimeZone('UTC');
            $paymentDateTime = null;
            if (isset($leadData['date_created']) && $leadData['date_created'] !== "") {
                $paymentDateTime = \DateTime::createFromFormat("Y-m-d H:i:s", $leadData['date_created'], $timezone)->setTimezone($timezone);
            }

            if ($paymentDateTime instanceof \DateTime && $paymentDateTime->getTimestamp() == $company->getPaymentDate()) {
                // payment already exists. return to search other payments.
                $msg = sprintf("Payment for company %s already exists.", $company->getTitle());
                $this->logger->info($msg);
                return $msg;
            }

            if (null !== $company->getId() && in_array($company->getPaymentType(), array('Annual', 'Monthly', 'Free')) && $paymentType === 'Free') {
                // company can't used Free account. return to search other payments.
                $msg = sprintf("Company \"%s\" can't used Free account.", $company->getTitle());
                $this->logger->info($msg);
                return $msg;
            }

            if (null !== $user->getId()) {
                $adminForCompany = $em->getRepository('SafeStartApi\Entity\Company')->findOneBy(array(
                    'admin'   => $user,
                ));
                if ($adminForCompany !== null) {
                    if($adminForCompany->getDeleted()) {
                        $adminForCompany->setAdmin(null);
                        $em->flush($adminForCompany);
                        $msg = sprintf(
                            "[Conflict data] This user with email %s is admin for deleted company %s. \nAdmin for company has been removed.",
                            $user->getEmail(),
                            $adminForCompany->getTitle());
                        $this->logger->info($msg);
                    } else {
                        // this user is admin for another company. return to search other payments.
                        $msg = sprintf("[Invalid data] This user with email %s is admin for another company.", $user->getEmail());
                        $this->logger->info($msg);
                        return $msg;
                    }
                }
            }

            $dateTime = new \DateTime();
            $dateTime->setTimezone($timezone);
            $oldExpiryDate = $company->getExpiryDate();

            if(is_int($oldExpiryDate) && $oldExpiryDate > 0) {
                $oldExpiryDate = new \DateTime();
                $oldExpiryDate->setTimestamp($company->getExpiryDate());
            }

            if ($oldExpiryDate instanceof \DateTime && $paymentDateTime instanceof \DateTime) {
                if ($oldExpiryDate > $paymentDateTime) {
                    $dateTime = clone $oldExpiryDate;
                } else {
                    $dateTime = clone $paymentDateTime;
                }
            } elseif($paymentDateTime instanceof \DateTime) {
                $dateTime = clone $paymentDateTime;
            }

            $restricted        = true;
            $unlim_users       = true;
            $unlim_expiry_date = false;
            $max_users         = 0;
            $max_vehicles      = (int)isset($leadData['_detailsData']['number_of_vehicles']) ? $leadData['_detailsData']['number_of_vehicles'] : 0;
            switch ($paymentType) {
                case 'Annual':
                    $expiry_date = $dateTime->add(new \DateInterval('P1Y'));
                    break;
                case 'Monthly':
                    $expiry_date = $dateTime->add(new \DateInterval('P1M'));
                    break;
                case 'Free':
                default:
                    $expiry_date  = $dateTime->add(new \DateInterval('P14D'));
                    $unlim_users  = false;
                    $max_users    = 1;
                    $max_vehicles = 1;
                    break;
            }

            $company->setRestricted($restricted);
            $company->setUnlimUsers($unlim_users);
            $company->setUnlimExpiryDate($unlim_expiry_date);
            $company->setMaxUsers($max_users);
            $company->setMaxVehicles($max_vehicles);
            $company->setExpiryDate($expiry_date);
            $company->setPaymentDate($paymentDateTime);
            $company->setPaymentType($paymentType);

            $sentEmail = false;
            if (null === $user->getId()) {
                $em->persist($user);
                $sentEmail = true;
            }

            $company->setAdmin($user);
            $em->persist($company);

            $user->setCompany($company);
            $user->setRole('companyAdmin');

            $em->flush();
            if($sentEmail) {
                $password = substr(md5($user->getId() . time() . rand()), 0, 6);
                $user->setPlainPassword($password);
                $em->flush();

                $this->logger->info("Send email to ".$user->getEmail());
                $config = $this->getServiceLocator()->get('Config');
                try {
                    $this->MailPlugin()->send('Credentials', $user->getEmail(), 'creds.phtml', array(
                        'username'              => $user->getUsername(),
                        'firstName'             => $user->getFirstName(),
                        'password'              => $password,
                        'siteUrl'               => $config['params']['site_url'],
                        'emailStaticContentUrl' => $config['params']['email_static_content_url']
                    ));
                } catch(\Exception $e) {
                    $this->logger->info("Failed send email to ".$user->getEmail());
                }
            }
        } else {
            $em->clear();
            return false;
        }

        return true;
    }
}
