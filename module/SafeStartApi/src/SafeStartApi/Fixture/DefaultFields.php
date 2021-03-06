<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\DefaultField;

class DefaultFields extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $csvFile = __DIR__ . "/DefaultFields.csv";
        $csvContent = file_get_contents($csvFile);
        $csvContent = str_replace("\r\n", PHP_EOL, $csvContent);
        $csvContent = preg_replace("/" . PHP_EOL . "$/", "", $csvContent);
        $csvLines = explode("\n", $csvContent);

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

        foreach ($csv as $row) {
            $field = new DefaultField();
            $field->setType($row['type']);
            $field->setOrder($row['sort_order']);
            $field->setTitle($row['title']);
            $field->setEnabled($row['enabled']);
            $field->setAdditional($row['additional']);
            $field->setTriggerValue($row['trigger_value']);
            $field->setAlertTitle($row['alert_title']);
            $field->setDescription($row['description']);
            $field->setAlertDescription($row['alert_description']);
            $field->setAlertCritical(1);
            if (!empty($row['parent_id'])) {
                $field->setParent($this->getReference('default-field-' . $row['parent_id']));
            }
            /*
            if (!empty($row['author_id'])) {
                $field->setParent($this->getReference('user-' . $row['author_id']));
            }
            */
            $manager->persist($field);
            $manager->flush();
            //Associate a reference for other fixtures
            $this->addReference('default-field-' . $row['id'], $field);
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
