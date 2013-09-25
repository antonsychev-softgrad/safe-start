<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\CheckList;

class Checklists extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $csvFile = __DIR__ . "/Checklists.csv";
        $csvContent = file_get_contents($csvFile);
        $csvLines = explode("\r\n", $csvContent);

        $delimiter = '|';
        $csv = array();
        $csvTitles = str_getcsv($csvLines[0], $delimiter);
        foreach($csvLines as $line) {
            $params = array();
            foreach(str_getcsv($line, $delimiter) as $key => $value) {
                $params[$csvTitles[$key]] = $value;
            }
            $csv[] = $params;
        }
        unset($csv[0]);

        foreach ($csv as $row) {
            $checkList = new Checklist();
            $checkList->setHash($row['hash']);
            $checkList->setVehicle($this->getReference('vehicle-' . $row['vehicle_id']));
            $checkList->setUser($this->getReference('usual-user' . $row['user_id']));
            $checkList->setFieldsStructure($row['fields_structure']);
            $checkList->setFieldsData($row['fields_data']);
            $checkList->setGpsCoords($row['gps_coords']);
            $checkList->setCurrentOdometer($row['current_odometer']);
            $checkList->setCurrentOdometerHours($row['current_odometer_hours']);
            $manager->persist($checkList);
            $manager->flush();
            //Associate a reference for other fixtures
            $this->addReference('checklist-' . $row['id'], $checkList);
        }

    }

    /**
     * Get the order of this execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }
}