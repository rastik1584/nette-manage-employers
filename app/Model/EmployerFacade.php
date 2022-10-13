<?php

namespace App\Model;

use Nette;

final class EmployerFacade
{

    public static function genderList()
    {
        return [
            'Muž' => 'Muž',
            'Žena' => 'Žena',
            'Iné' => 'Iné'
        ];
    }

    /**
     * Structure from employers
     * @return array[]
     */
    public static function structure()
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Meno zamestnanca',
                'default' => null,
                'css_class' => 'form-control'
            ],
            'gender' => [
                'type' => 'select',
                'options' => self::genderList(),
                'label' => 'Pohlavie',
                'default' => null,
                'css_class' => 'form-control'
            ],
            'age' => [
                'type' => 'integer',
                'label' => 'Vek',
                'default' => null,
                'css_class' => 'form-control'
            ]
        ];
    }

    /**
     * regenerate employer structure with add or remove field
     * @return void
     */
    public static function regenerateStructure()
    {
        $xml = simplexml_load_file(XML_DB_FILE);
        foreach($xml->Employers->Employer as $employer) {
            if(count($employer) < count(self::structure())) {
                // add
                foreach (self::structure() as $field => $data) {
                    if(!isset($employer->$field)) {
                        $employer->addChild($field, $data['default']);
                    }
                }
            } elseif(count($employer) > count(self::structure())) {
                // remove
                foreach($employer as $key => $empty) {
                    if(!isset(self::structure()[$key])) {
                        $dom = dom_import_simplexml($employer->$key);
                        $dom->parentNode->removeChild($dom);
                    }
                }
            }
        }
        $xml->asXML(XML_DB_FILE);
    }
}