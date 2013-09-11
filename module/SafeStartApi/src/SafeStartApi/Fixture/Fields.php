<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Field;

class Fields extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $csvFile = __DIR__ . "/DefaultFields.csv";
        $csvContent = file_get_contents($csvFile);
        $csvLines = explode("\r\n", $csvContent);

        $delimiter = ';';
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

        foreach ($csv as $key => $row) {
            $field = new Field();
            $field2 = new Field();
            $field->setVehicle($this->getReference('vehicle-1'));
            $field2->setVehicle($this->getReference('vehicle-2'));
            $field->setType($row['type']);
            $field2->setType($row['type']);
            $field->setOrder($row['sort_order']);
            $field2->setOrder($row['sort_order']);
            $field->setTitle($row['title']);
            $field2->setTitle($row['title']);
            $field->setEnabled($row['enabled']);
            $field2->setEnabled($row['enabled']);
            $field->setAdditional($row['additional']);
            $field2->setAdditional($row['additional']);
            $field->setTriggerValue($row['trigger_value']);
            $field2->setTriggerValue($row['trigger_value']);
            $field->setAlertTitle($row['alert_title']);
            $field2->setAlertTitle($row['alert_title']);
            $field->setAlertDescription('Description of vehicle fault should be here');
            $field2->setAlertDescription('Description of vehicle fault should be here');
            if (!empty($row['parent_id'])) {
                $field->setParent($this->getReference('field-' . $row['parent_id']));
                $field2->setParent($this->getReference('field2-' . $row['parent_id']));
            }
            /*
            if (!empty($row['author_id'])) {
                $field->setParent($this->getReference('user-' . $row['author_id']));
            }
            */
            $manager->persist($field);
            $manager->persist($field2);
            $manager->flush();
            //Associate a reference for other fixtures
            $this->addReference('field-' . $row['id'], $field);
            $this->addReference('field2-' . $row['id'], $field2);
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