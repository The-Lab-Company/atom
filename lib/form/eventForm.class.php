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
        $this->setValidator('date', new sfValidatorString());
        $this->setWidget('date', new sfWidgetFormInput());
    }

    public function configureStartDate()
    {
        $this->setValidator('startDate', new sfValidatorString());
        $this->setWidget('startDate', new sfWidgetFormInput());
    }

    public function configureEndDate()
    {
        $this->setValidator('endDate', new sfValidatorString());
        $this->setWidget('endDate', new sfWidgetFormInput());
    }

    public function configureType()
    {
        $choices = [];
        $eventTypes = sfIsadPlugin::eventTypes();

        foreach ($eventTypes as $item) {
            $choices += [
                $this->context->routing->generate(
                    null, [$item, 'module' => 'term']
                ) => $item->__toString(),
            ];
        }

        $this->setValidator('type', new sfValidatorChoice(
            ['choices' => $choices]
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
