<?php

/**
 * Events form.
 *
 * @author     David Juhasz <david@artefactual.com>
 */
class EventForm extends sfForm
{
    public function configureId()
    {
        $this->setValidator('id', new sfValidatorString());
        $this->setWidget('id', new sfWidgetFormInputHidden());
    }

    public function configureDate()
    {
        $this->setValidator('date', new sfValidatorString(
            ['max_length' => 255]
        ));
        $this->setWidget('date', new sfWidgetFormInput());
    }

    public function configureStartDate()
    {
        $this->setValidator('startDate', new sfValidatorRegex(
            ['max_length' => 10, 'pattern' => '/\d{4}-?\d{0,2}-?\d{0,2}/'],
            [
                'invalid' =>
                'Start date must be in the format YYYY-MM-DD or YYYYMMDD'
            ],
        ));
        $this->setWidget('startDate', new sfWidgetFormInput());
    }

    public function configureEndDate()
    {
        $this->setValidator('endDate', new sfValidatorRegex(
            ['max_length' => 10, 'pattern' => '/\d{4}-?\d{0,2}-?\d{0,2}/'],
            [
                'invalid' =>
                'End date must be in the format YYYY-MM-DD or YYYYMMDD'
            ],
        ));
        $this->setWidget('endDate', new sfWidgetFormInput());
    }

    public function configureType()
    {
        $choices = [];
        $eventTypes = sfIsadPlugin::eventTypes();

        foreach ($eventTypes as $item) {
            $route = $this->context->routing->generate(
                null,
                [$item, 'module' => 'term']
            );
            $choices += [$route => $item->__toString()];
        }

        $this->setValidator('type', new sfValidatorChoice(
            ['choices' => array_keys($choices)]
        ));
        $this->setWidget('type', new sfWidgetFormSelect(
            ['choices' => $choices]
        ));
    }

    public function configure()
    {
        $this->context = sfContext::getInstance();

        $this->getWidgetSchema()->setNameFormat('event[%s]');

        $this->configureId();
        $this->configureDate();
        $this->configureStartDate();
        $this->configureEndDate();
        $this->configureType();
    }
}
