<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Form for adding and editing related events.
 *
 * @author     David Juhasz <david@artefactual.com>
 */
class InformationObjectEventComponent extends EventEditComponent
{
    // Don't update the search index when saving an event object
    public $indexOnSave = false;

    // Arrays not allowed in class constants
    public static $NAMES = [
        'id',
        'actor',
        'date',
        'endDate',
        'startDate',
        'description',
        'place',
        'type',
    ];

    /**
     * Add event to QubitInformationObject::eventsRelatedByobjectId[] list
     * to ensure the event object is create after the QubitInformatinObject.
     */
    public function addEvent(QubitEvent $event): QubitEvent
    {
        $this->resource->eventsRelatedByobjectId[] = $event;

        return $event;
    }

    public function getEvents()
    {
        return $this->resource->eventsRelatedByobjectId;
    }

    /**
     * Add event sub-forms to $this->events form.
     *
     * Add one event sub-form for each event linked to $resource, plus one blank
     * event sub-form for adding a new linked event.
     */
    protected function addEventForms()
    {
        $i = 0;

        // Add one event sub-form for each event related to this resource, to
        // allow editing the existing events
        foreach ($this->getEvents() as $event) {
            $form = new EventForm($this->getFormDefaults($event));
            $form->getWidgetSchema()->setNameFormat("events[{$i}][%s]");

            // Embed the event sub-form into the $this->events form
            $this->events->embedForm($i++, $form);
        }

        // Add a blank event sub-form to allow adding a new event
        $form = new EventForm(['type' => $this->getEventTypeDefault()]);
        $form->getWidgetSchema()->setNameFormat("events[{$i}][%s]");
        $this->events->embedForm($i, $form);
    }
}
