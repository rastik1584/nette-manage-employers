<?php

namespace App\Forms;

use App\Model\EmployerFacade;
use Nette\Application\UI\Form;

class EmployersFormFactory
{
    /**
     *  create form
     * @return Form
     */
    public function create() : Form
    {
        $form = new Form;
        $form = $this->setFields($form);
        return $form;
    }

    /**
     * set field for form ( fields define in EmployerFacade structure type keyword )
     * @param $form
     * @return mixed
     */
    private function setFields($form)
    {
        foreach(EmployerFacade::structure() as $key => $structure) {
            $css_class = isset($structure['css_class']) ? $structure['css_class'] : '';

            if($structure['type'] == 'text') {
                $form->addText($key, $structure['label'])
                    ->setHtmlAttribute('class', $css_class);
            }
            if($structure['type'] == 'integer') {
                $form->addInteger($key, $structure['label'])
                    ->setHtmlAttribute('class', $css_class);
            }
            if($structure['type'] == 'select') {
                $form->addSelect('gender', $structure['label'], $structure['options'])
                    ->setHtmlAttribute('class', $css_class);
            }
        }

        $form->addSubmit('send', 'Uložiť')->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}