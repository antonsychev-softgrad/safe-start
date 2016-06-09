<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use SafeStartApi\Entity\Vehicle;
use SafeStartApi\Entity\Alert;

class AlertReminderPlugin extends AbstractPlugin
{
    public function sendAlertReminders($debug = false)
    {
		$this->debug = $debug;

        $this->setEntityManager();

        // Send overdue reminder emails
        $this->sendOverdueReminders();

        // Send warning emails (24 hrs)
        $this->sendWarningReminders();

        // Send monitor reminder emails
        $this->sendMonitorReminders();

        if ($this->debug)
        {
            flush();
            exit();
        }
    }

    public function sendReminder($alert, $viewName)
    {
        $this->setEntityManager();

        $config = $this->getController()->getServiceLocator()->get('Config');

        $title = $alert->getDescription()
            ? $alert->getDescription()
            : ($alert->getField()
                ? ($alert->getField()->getAlertDescription()
                   ? $alert->getField()->getAlertDescription() : $alert->getField()->getAlertTitle())
                : '');

        $plantId = '';
        $vehicle = $alert->getVehicle();
        if ($vehicle)
        {
            $company = $vehicle->getCompany();
            if ($company)
            {
                $users = self::getUsersForAlert($vehicle);
                foreach ($users as $email => $user)
                {
                    $plantId = $vehicle->getPlantId();
                    $checkList = $alert->getCheckList();
                    $faultReport = $alert->getFaultReport();
                    $alertUser = $checkList ? $checkList->getUser() : ($faultReport ? $faultReport->getUser() : null);
                    $userName = $alertUser ? $alertUser->getFullName() : '(unknown)';

                    $this->getController()->MailPlugin()->send(
                        "Vehicle Alert Message ($plantId)",
                        $email,
                        $viewName,
                        array(
                           'plantId' => $plantId,
                           'user' => $userName,
                           'alertDescription' => $title,
                           'creationDate' => $alert->getCreationDate()->format('m/d/Y'),
                           'dueDate' => $alert->getDueDate()->format('m/d/Y'),
                           'actionLink' =>  $config['params']['site_url']. '/#company/'. $company->getId(),
                           'site' => $config['params']['site_url'],
                           'images' => $alert->getImages()
                        ),
                        null
                    );
                }
            }
        }
    }

    private function setEntityManager()
    {
        if (!isset($this->em) || !$this->em)
        {
            if (isset($this->getController()->em) && $this->getController()->em)
            {
                $this->em = $this->getController()->em;
            }
            else
            {
                $this->em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            }
        }
    }

    // Send overdue reminder emails
    private function sendOverdueReminders()
    {
        $mailStatus = \SafeStartApi\Entity\Alert::MAIL_STATUS_SENT_OVERDUE;
        $condition = 'AND a.due_date < :parameter ';
        $parameter = new \DateTime();

        if ($this->debug)
        {
echo "Send overdue reminder emails";
echo "<br/>\n";
echo $parameter->format('Y-m-d H:i:s');
echo "<br/>\n";
        }

        $this->sendReminders($mailStatus, $condition, $parameter, 'overduealertmail.phtml');
    }

    // Send warning emails (24 hrs)
    private function sendWarningReminders()
    {
        $mailStatus = \SafeStartApi\Entity\Alert::MAIL_STATUS_SENT_24HOURS;
        $condition = 'AND a.due_date < :parameter ';
        $dueDate24 = new \DateTime();
        $dueDate24->add(new \DateInterval('P1D'));
        $parameter = $dueDate24;

        if ($this->debug)
        {
echo "Send warning emails (24 hrs)";
echo "<br/>\n";
echo $parameter->format('Y-m-d H:i:s');
echo "<br/>\n";
        }

        $this->sendReminders($mailStatus, $condition, $parameter, 'warningmail.phtml');
    }

    // Send monitor reminder emails (7 days)
    private function sendMonitorReminders()
    {
        $mailStatus = \SafeStartApi\Entity\Alert::MAIL_STATUS_SENT_7DAYS;
        $condition = 'AND a.monitor = 1 AND a.due_date < :parameter ';
        $dueDate7 = new \DateTime();
        $dueDate7->add(new \DateInterval('P7D'));
        $parameter = $dueDate7;

        if ($this->debug)
        {
echo "Send monitor reminder emails (7 days)";
echo "<br/>\n";
echo $parameter->format('Y-m-d H:i:s');
echo "<br/>\n";
        }

        $this->sendReminders($mailStatus, $condition, $parameter, 'monitorwarningmail.phtml');
    }

    // Send reminder emails
    private function sendReminders($mailStatus, $condition, $parameter, $viewName)
    {
        $status = \SafeStartApi\Entity\Alert::STATUS_NEW;
        $where = 'WHERE a.deleted = 0 AND a.status = :status AND a.mail_status < :mailStatus ';
        $sql = 'SELECT a FROM SafeStartApi\Entity\Alert a '.
            $where.
            $condition;

        if ($this->debug)
        {
echo "$sql";
echo "<br/>\n";
        }

        $query = $this->em->createQuery($sql);
        $query->setParameter('status', $status);
        $query->setParameter('mailStatus', $mailStatus);
        $query->setParameter('parameter', $parameter);
        $items = $query->getResult();

        foreach ($items as $alert) {
        if ($this->debug)
        {
echo $alert->getId();
echo "<br/>\n";
        }

            self::sendReminder($alert, $viewName);

            $alert->setMailStatus($mailStatus);
            $this->em->persist($alert);
            $this->em->flush();
        }
    }

    private function getUsersForAlert($vehicle)
    {
        $query = $this->em->createQuery('SELECT u FROM SafeStartApi\Entity\User u WHERE u.deleted = 0 AND u.company = ?1');
        $query->setParameter(1, $vehicle->getCompany());
        $companyUsers = $query->getResult();

        $users = array();

        // Get company admins and managers
        foreach($companyUsers as $user)
        {
            $role = $user->getRole();
            if ($role == 'companyAdmin'
               || $role == 'companyManager')
            {
                $email = $user->getEmail();
                $users[$email] = $user;
            }
        }

        // Get responsible users
        foreach($vehicle->getResponsibleUsers() as $user)
        {
            $email = $user->getEmail();
            if (!isset($users[$email]))
            {
                $users[$email] = $user;
            }
        }

        return $users;
    }
}