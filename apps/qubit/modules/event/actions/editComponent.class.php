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

class EventEditComponent extends sfComponent
{
    public function processForm()
    {
        // Events should index the related resource only when
        // they are managed from the actors form.
        $indexOnSave = false;
        if ($this->resource instanceof QubitActor) {
            $indexOnSave = true;
        }

        foreach ($this->request->events as $item) {
            // Continue only if user typed something
            foreach ($item as $value) {
                if (0 < strlen($value)) {
                    break;
                }
            }

            if (1 > strlen($value)) {
                continue;
            }

            if (!isset($this->request->sourceId) && isset($item['id'])) {
                $params = $this->context->routing->parse(
                    Qubit::pathInfo($item['id'])
                );

                // Do not add exiting events to the eventsRelatedByobjectId
                // or events array, as they could be deleted before saving
                // the resource
                $this->event = $params['_sf_route']->resource;
            } elseif ($this->resource instanceof QubitActor) {
                $this->resource->events[] = $this->event = new QubitEvent();
            } else {
                $this->resource->eventsRelatedByobjectId[] =
                    $this->event = new QubitEvent();
            }

            foreach ($this->form as $field) {
                if (isset($item[$field->getName()])) {
                    $this->processField($field);
                }
            }

            // Save existing events as they are not attached
            // to the eventsRelatedByobjectId or events array
            if (isset($this->event->id)) {
                $this->event->indexOnSave = $indexOnSave;
                $this->event->save();
            }
        }

        // Stop here if duplicating
        if (isset($this->request->sourceId)) {
            return;
        }

        if (isset($this->request->deleteEvents)) {
            foreach ($this->request->deleteEvents as $item) {
                var_dump('DELETE', $item);
                $params = $this->context->routing->parse(
                    Qubit::pathInfo($item)
                );
                $event = $params['_sf_route']->resource;
                $event->indexOnSave = $indexOnSave;
                //$event->delete();
            }
        }
    }

    public function execute($request)
    {
        $i = 0;

        // Add one event form for each event related to this resource
        foreach ($this->resource->eventsRelatedByobjectId as $event) {
            $form = new eventForm($this->getFormDefaults($event));
            $form->getWidgetSchema()->setNameFormat("events[{$i}][%s]");

            // Embed this form into the main form for the page
            $this->form->embedForm($i++, $form);
        }

        // Add a blank event form to allow adding a new event
        $form = new eventForm(['type' => $this->getEventTypeDefault()]);
        $this->form->embedForm($i++, $form);
    }

    protected function getFormDefaults($event)
    {
        return [
            'id' => $event->id,
            'date' => $event->date,
            'startDate' => Qubit::renderDate($event->startDate),
            'endDate' => Qubit::renderDate($event->endDate),
            'type' => $this->getEventTypeDefault($event),
        ];
    }

    protected function getEventTypeDefault($event = null)
    {
        if (isset($event, $event->type)) {
            $term = $event->type;
        } else {
            // Default event type is creation
            $term = QubitTerm::getById(QubitTerm::CREATION_ID);
        }

        if (!isset($term)) {
            return null;
        }

        return $this->context->routing->generate(
            null, [$term, 'module' => 'term']
        );
    }

    protected function processField($field)
    {
        switch ($field->getName()) {
            case 'id':
                $value = $this->form->getValue('id');
                if (isset($value)) {
                    $this->event[$field->getName()] = $value;
                }

                break;

            case 'type':
            case 'resourceType':
                unset($this->event[$field->getName()]);

                $value = $this->form->getValue($field->getName());
                if (isset($value)) {
                    $params = $this->context->routing->parse(
                        Qubit::pathInfo($value)
                    );
                    $this->event[$field->getName()] =
                        $params['_sf_route']->resource;
                }

                break;

            case 'startDate':
            case 'endDate':
                $value = $this->form->getValue($field->getName());
                if (
                    isset($value)
                    && preg_match('/^\d{8}\z/', trim($value), $matches)
                ) {
                    $value = substr($matches[0], 0, 4).'-'.
                        substr($matches[0], 4, 2).'-'.substr($matches[0], 6, 2);
                } elseif (
                    isset($value)
                    && preg_match('/^\d{6}\z/', trim($value), $matches)
                ) {
                    $value = substr($matches[0], 0, 4).'-'.
                        substr($matches[0], 4, 2);
                }

                $this->event[$field->getName()] = $value;

                break;

            default:
                $this->event[$field->getName()] = $this->form->getValue(
                    $field->getName()
                );
        }
    }
}
