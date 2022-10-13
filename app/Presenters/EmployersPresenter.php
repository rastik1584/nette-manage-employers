<?php

namespace App\Presenters;

use App\Forms\EmployersFormFactory;
use App\Model\EmployerFacade;
use Nette;
use Nette\Application\UI\Form;

final class EmployersPresenter extends Nette\Application\UI\Presenter
{
    public $formFactory;

    public function __construct(EmployersFormFactory $formFactory   )
    {
        parent::__construct();

        $this->formFactory = $formFactory;
    }

    /**
     * Render employer list template
     * @return void
     */
    public function renderDefault()
    {
        $xml = $this->getXmlFile();
        $content_data = json_decode(json_encode($xml->Employers), true );

        $this->template->content_data = isset($content_data['Employer']) ? $content_data['Employer'] : $content_data;
        $this->template->structure = EmployerFacade::structure();
    }

    /**
     * render create employer template
     * @return void
     */
    public function renderCreate()
    {
        $structure = EmployerFacade::structure();
        $this->template->structure = $structure;
    }

    /**
     * Render edit employer template
     * @param $id
     * @return void
     */
    public function renderEdit($id)
    {
        $xml = $this->getXmlFile();
        $data = $xml->Employers->xpath("//Employer[@id=".$id."]");
        $data = array_merge((array)$data[0], ['id' => $id]);
        $this->getComponent('employersEditForm')->setDefaults($data);
        $this->template->data = $data;
    }

    public function actionRemove($id)
    {
        $xml = $this->getXmlFile();
        foreach($xml->Employers->xpath("//Employer[@id=".$id."]") as $node) {
            $dom=dom_import_simplexml($node);
            $dom->parentNode->removeChild($dom);
        }

        $xml->asXML(XML_DB_FILE);
        $this->flashMessage('Úspešne zmazaný zamestnanec');
        $this->redirect('Employers:default');

    }

    /**
     * regenerate employers structure
     * @return void
     */
    public function actionRegenerate()
    {
        EmployerFacade::regenerateStructure();
        $this->flashMessage('Štruktúra zamestnancov bola upravená');
        $this->redirect('Employers:default');
    }

    /**
     * Create employers form
     * @return Form
     */
    protected function createComponentEmployersForm(): Form
    {
        $form = $this->formFactory->create();
        $form->onSuccess[] = [$this, 'employersCreateFormSucceed']; // a přidáme handler
        return $form;
    }

    /**
     * Add employer with form data
     * @param Form $form
     * @param $data
     * @return void
     * @throws Nette\Application\AbortException
     */
    public function employersCreateFormSucceed(Form $form, $data): void
    {
        ob_start();
        $xml = $this->getXmlFile();
        $id = count($xml->Employers->Employer) + 1;
        $employer = $xml->Employers->addChild('Employer');
        $employer->addAttribute('id', $id);
        foreach($data as $field => $value) {
            $employer->addChild($field, $value);
        }
        $xml->asXML(XML_DB_FILE);

        $this->flashMessage('Zamestnanec bol pridaný');
        $this->redirect('Employers:default');
    }

    /**
     * Edit employer form
     * @return Form
     */
    protected function createComponentEmployersEditForm(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $form->onSuccess[] = [$this, 'employerUpdateFormSucceed'];
        return $form;
    }

    /**
     * Edit employer with form data
     * @param Form $form
     * @param $data
     * @return void
     * @throws Nette\Application\AbortException
     */
    public function employerUpdateFormSucceed(Form $form, $data)
    {
        $xml = $this->getXmlFile();
        $xml_data = $xml->Employers->xpath("//Employer[@id=".$data['id']."]");
        $xml_data = $xml_data[0];
        foreach($data as $field => $value) {
            if($field !== 'id' ) {
                $xml_data->$field = $value;
            }
        }
        $xml->asXML(XML_DB_FILE);
        $this->flashMessage('Zamestnanec '.$data['name'] . ' bol aktualizovaný');
        $this->redirect('Employers:default');
    }

    /**
     * Helper function return xml loaded file
     * @return \$1|false|\SimpleXMLElement
     */
    private function getXmlFile()
    {
        return simplexml_load_file(XML_DB_FILE);
    }
}