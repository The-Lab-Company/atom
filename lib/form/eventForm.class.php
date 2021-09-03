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
        $this->setValidator('id', new sfValidatorString(
            ['max_length' => 255]
        ));
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
                'invalid' => 'Start date must be in the format YYYY-MM-DD or YYYYMMDD',
            ],
        ));
        $this->setWidget('startDate', new sfWidgetFormInput());
    }

    public function configureEndDate()
    {
        $this->setValidator('endDate', new sfValidatorRegex(
            ['max_length' => 10, 'pattern' => '/\d{4}-?\d{0,2}-?\d{0,2}/'],
            [
                'invalid' => 'End date must be in the format YYYY-MM-DD or YYYYMMDD',
            ],
        ));
        $this->setWidget('endDate', new sfWidgetFormInput());
    }

    public function configureType()
    {
        $choices = [];
        $eventTypes = sfIsadPlugin::eventTypes();

        foreach ($eventTypes as $item) {
            $route = sfContext::getInstance()->routing->generate(
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

    public function configureActor()
    {
        $this->setValidator('actor', new sfValidatorString());

        $this->setWidget('actor', new sfWidgetFormSelect(['choices' => []]));

        $this->getWidgetSchema()->actor->setHelp(
            $sfContext::getInstance()->i18n->__(
                'Use the actor name field to link an authority record to this description. Search for an existing name in the authority records by typing the first few characters of the name. Alternatively, type a new name to create and link to a new authority record.'
            )
        );
    }

    public function configureDescription()
    {
        $this->setValidator('description', new sfValidatorString());
        $this->setWidget('description', new sfWidgetFormInput());
    }

    public function configurePlace()
    {
        $this->setValidator('place', new sfValidatorString());
        $this->setWidget('place', new sfWidgetFormSelect(['choices' => []]));

        $this->getWidgetSchema()->place->setHelp(
            sfContext::getInstance()->i18n->__(
                'Search for an existing term in the places taxonomy by typing the first few characters of the term name. Alternatively, type a new term to create and link to a new place term.'
            )
        );
    }

    public function configure()
    {
        $this->getWidgetSchema()->setNameFormat('event[%s]');

        // Configure fields included in all event forms
        $this->configureId();
        $this->configureDate();
        $this->configureStartDate();
        $this->configureEndDate();
        $this->configureType();
    }
}
