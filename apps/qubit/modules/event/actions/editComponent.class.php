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
    protected $finalEventIds = [];

    public function execute($request)
    {
        // Embed "events" form (and sub-forms) in the main form for the page
        $this->events = new QubitForm();
        $this->form->embedForm('events', $this->events);
        $this->addEventForms();
    }

    public function hasRequiredData($form)
    {
        foreach ($form as $field) {
            if (!empty($field->getValue())) {
                return true;
            }
        }

        return false;
    }

    public function processEventForm($form, $data)
    {
        // Continue only if user typed something
        if (!$this->hasRequiredData($form)) {
            return;
        }

        if (!isset($this->request->sourceId) && isset($data['id'])) {
            // Get the existing QubitEvent object
            $event = QubitEvent::getById($form['id']->getValue());

            // Do not add exiting events to the
            // $informationObject->eventsRelatedByobjectId or $actor->events
            // array, as they could be deleted before saving the resource
            $this->finalEventIds[] = $event->id;
        } else {
            // Create a new QubitEvent object, and link it to $this->resource.
            // See the actor and informationObject subclasses for addEvent()
            // method.
            $event = $this->addEvent(new QubitEvent());
        }

        if (!isset($event)) {
            return;
        }

        foreach ($form as $field) {
            var_dump('FORM: ', $form->getName());
            if (isset($data[$field->getName()])) {
                //$this->processField($this->form['events'][$i][$field->getName()], $event);
            }
        }

        // Save existing events as they are not attached to the
        // eventsRelatedByobjectId or events array
        if (isset($event->id)) {
            // Index on save for actors, but not informationObjects
            $event->indexOnSave = $this->indexOnSave;
            $event->save();
        }
    }

    public function processForms()
    {
        foreach ($this->request->events as $i => $postData) {
            $this->processEventForm($this->events[$i], $postData);
        }

        // Stop here if duplicating a QubitInformationObject
        if (isset($this->request->sourceId)) {
            return;
        }

        // Delete events marked for deletion
        $this->deleteDeletedEvents();
    }

    protected function deleteDeletedEvents()
    {
        if (!isset($this->request->deleteEvents)) {
            return;
        }

        foreach ($this->request->deleteEvents as $eventUri) {
            $params = $this->context->routing->parse(
                Qubit::pathInfo($eventUri)
            );
            $event = $params['_sf_route']->resource;

            if (isset($event) && QubitEvent::class === class_name($event)) {
                $event->indexOnSave = $this->indexOnSave;
                $event->delete();
            }
        }
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

    protected function processField($field, &$event)
    {
        var_dump($field->getName(), $field->getValue());

        switch ($field->getName()) {
            case 'actor':
                unset($this->event->actor);

                $value = $this->form->getValue('actor');
                if (isset($value)) {
                    $params = $this->context->routing->parse(Qubit::pathInfo($value));
                    $this->event->actor = $params['_sf_route']->resource;
                }

                break;

            case 'id':
                // The event id is already set by this point
                break;

            case 'endDate':
            case 'startDate':
                $value = $field->getValue();

                if (empty($value)) {
                    $event[$field->getName()] = null;

                    return;
                }

                // Parse YYYYMMDD format
                if (preg_match('/^\d{8}\z/', trim($value), $matches)) {
                    $value = substr($matches[0], 0, 4).'-'.
                        substr($matches[0], 4, 2).'-'.
                        substr($matches[0], 6, 2);
                } elseif (preg_match('/^\d{6}\z/', trim($value), $matches)) {
                    // Parse YYYYMM format
                    $value = substr($matches[0], 0, 4).'-'.
                        substr($matches[0], 4, 2);
                }

                $event[$field->getName()] = $value;

                break;

            case 'place':
                // Get related term id
                $value = $this->form->getValue('place');
                if (!empty($value)) {
                    $params = $this->context->routing->parse(Qubit::pathInfo($value));
                    $termId = $params['_sf_route']->resource->id;
                }

                // Get term relation
                if (isset($this->event->id)) {
                    $relation = QubitObjectTermRelation::getOneByObjectId($this->event->id);
                }

                // Nothing to do
                if (!isset($termId) && !isset($relation)) {
                    break;
                }

                // The relation needs to be deleted/updated independently
                // if the event exits, otherwise when deleting, it will try to
                // save it again from the objectTermRelationsRelatedByobjectId array.
                // If the event is new, the relation needs to be created and attached
                // to the event in the objectTermRelationsRelatedByobjectId array.
                if (!isset($termId) && isset($relation)) {
                    $relation->delete();

                    break;
                }

                if (isset($termId, $relation)) {
                    $relation->termId = $termId;
                    $relation->save();

                    break;
                }

                $relation = new QubitObjectTermRelation();
                $relation->termId = $termId;

                $this->event->objectTermRelationsRelatedByobjectId[] = $relation;

                break;

            case 'resourceType':
            case 'type':
                unset($event->type);

                $value = $field->getValue();

                if (!empty($value)) {
                    $route = $this->context->routing->parse(
                        Qubit::pathInfo($value)
                    );
                    $term = $route['_sf_route']->resource;
                }

                $event->type = $term;

                break;

            default:
                $event[$field->getName()] = $field->getValue();
        }
    }
}
