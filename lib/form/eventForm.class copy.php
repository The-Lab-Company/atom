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
        $this->setHelp(
            'date',
            $this->context->i18n->__(
                'Enter free-text information, including qualifiers or
                typographical symbols to express uncertainty, to change the way the date displays. If this field is not used, the default will
                be the start and end years only.'
            )
        );
        $form->setLabel('endDate', $this->context->i18n->__('End'));
    }

    public function configureStartDate()
    {
        $form->setValidator('startDate', new sfValidatorString());
        $form->setWidget('startDate', new sfWidgetFormInput());
        $form->setHelp(
            'startDate',
            $this->context->i18n->__(
                'Enter the start year. Do not use any qualifiers or
                typographical symbols to express uncertainty. Acceptable
                date formats: YYYYMMDD, YYYY-MM-DD, YYYY-MM, YYYY.'
            )
        );
        $form->setLabel('startDate', $this->context->i18n->__('Start'));
    }

    public function configureEndDate()
    {
        $this->setValidator('endDate', new sfValidatorString());
        $this->setWidget('endDate', new sfWidgetFormInput());
        $this->setHelp(
            'endDate',
            $this->context->i18n->__(
                'Enter the end year. Do not use any qualifiers or typographical
                symbols to express uncertainty. Acceptable date formats:
                YYYYMMDD, YYYY-MM-DD, YYYY-MM, YYYY.'
            )
        );
        $form->setLabel('endDate', $this->context->i18n->__('End'));
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

            // Default event type is creation
            if (QubitTerm::CREATION_ID == $item->id) {
                $this->setDefault(
                    'type',
                    $this->context->routing->generate(
                        null, [$item, 'module' => 'term']
                    )
                );
            }
        }

        $this->setValidator('type', new sfValidatorString());
        $this->setWidget('type', new sfWidgetFormSelect(
            ['choices' => $choices]
        ));
    }

    public function configure()
    {
        $this->widgetSchema->setNameFormat('event[%s]');
        $this->widgetSchema->setIdFormat('event_form_%s');

        $this->configureId();
        $this->configureDate();
        $this->configureStartDate();
        $this->configureEndDate();
        $this->configureType();
    }
}
