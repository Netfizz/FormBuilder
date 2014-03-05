<?php namespace Netfizz\FormBuilder;

use Former;
use Former\Form\Fields\Button;

class FormBuilder {


    protected $model;

    protected $method = 'put';

    protected $formElements;

    protected $tableInfo;

    public $form;


    public function __construct($model)
    {
        $this->model = $model;

        $this->tableInfo = $this->getTableInfo($this->model);

    }


    public function getForm()
    {
        $form = new \stdClass;

        $form->open = $this->getFormOpen();

        $form->elements = $this->getFormElements();

        $form->buttons = $this->getFormbuttons();

        $form->close = $this->getFormClose();

        $this->form = $form;

        return $this->form;
    }

    public function populate($item)
    {
        return Former::populate($item);
    }

    /**
     * Fetch a list of attributes for the
     * table, minus non-essentials.
     *
     * @param string $table
     * @return array
     */
    protected function getModelAttributes($table)
    {
        $names = array_keys($table);

        return array_diff($names, array('id', 'created_at', 'updated_at', 'deleted_at', 'password'));
    }




    /**
     * Generate form open string
     * @param  string $method
     * @param  string $model
     * @return string
     */
    protected function getFormOpen()
    {
        /*
        $models = Pluralizer::plural($model);

        if (preg_match('/edit|update|put|patch/i', $method))
        {
            return "{{ Form::model(\${$model}, array('method' => 'PATCH', 'route' => array('{$models}.update', \${$model}->id))) }}";
        }

        return "{{ Form::open(array('route' => '{$models}.store')) }}";
        */

        return Former::horizontal_open()
            ->id('MyForm')
            ->secure()
            ->rules(['name' => 'required'])
            ->method('PUT');
    }

    protected function getFormClose()
    {
        return Former::close();
    }


    protected function getFormButtons()
    {
        return  Former::actions()
            ->large_primary_submit('Submit')
            ->large_inverse_reset('Reset');
    }




    /**
     * Fetch Doctrine table info
     * @param  string $model
     * @return object
     */
    protected function getTableInfo($model)
    {
        $table = $model->getTable();

        return \DB::getDoctrineSchemaManager()->listTableDetails($table)->getColumns();
    }

    /**
     * Calculate correct Formbuilder method
     *
     * @param  string $name
     * @return string
     */
    protected function getInputType($name)
    {
        $dataType = $this->tableInfo[$name]->getType()->getName();

        $lookup = array(
            'string'  => 'text',
            'float'   => 'text',
            'date'    => 'text',
            'text'    => 'textarea',
            'boolean' => 'checkbox'
        );

        return array_key_exists($dataType, $lookup)
            ? $lookup[$dataType]
            : 'text';
    }

    /**
     * Dynamically create form elements
     *
     * @param  string $type
     * @param  string $element
     * @return string
     */
    protected function getFormElements()
    {
        $elements = array();
        $attributes = $this->getModelAttributes($this->tableInfo);

        foreach($attributes as $name)
        {
            $elements[$name] = $this->setElement($name);
        }

        return $elements;
    }

    protected function setElement($name)
    {
        $type = $this->getInputType($name);

        return Former::$type($name);
    }

} 