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

class sfIsadPluginEventComponent extends InformationObjectEventComponent
{
    // Arrays not allowed in class constants
    public static $NAMES = [
        'id',
        'date',
        'endDate',
        'startDate',
        'type',
    ];

    // TODO Refactor with parent::processForm()
    public function processForm()
    {
        $finalEventIds = [];

        foreach ($this->request->events as $item) {
            if (
                empty($item['date'])
                && empty($item['endDate'])
                && empty($item['startDate'])
            ) {
                // Skip this row if there is no date data
                continue;
            }

            if (!isset($this->request->sourceId) && isset($item['id'])) {
                $params = $this->context->routing->parse(
                    Qubit::pathInfo($item['id'])
                );

                // Do not add exiting events to the eventsRelatedByobjectId
                // array, as they could be deleted before saving the resource
                $this->event = $params['_sf_route']->resource;
                array_push($finalEventIds, $this->event->id);
            } else {
                $this->event = new QubitEvent();
                $this->resource->eventsRelatedByobjectId[] = $this->event;
            }

            foreach ($this->form as $field) {
                if (isset($item[$field->getName()])) {
                    $this->processField($field);
                }
            }

            // Save existing events as they are not attached
            // to the eventsRelatedByobjectId array
            if (isset($this->event->id)) {
                $this->event->indexOnSave = false;
                $this->event->save();
            }
        }

        // Delete the old events if they don't appear in the table (removed by
        // multiRow.js) Check date events as they are the only ones added in
        // this table
        foreach ($this->resource->getDates() as $item) {
            if (
                isset($item->id)
                && false === array_search($item->id, $finalEventIds)
            ) {
                // Will be indexed when description is saved
                $item->indexOnSave = false;

                // Only delete event if it has no associated actor
                if (!isset($item->actor)) {
                    $item->delete();
                } else {
                    // Handle specially as data wasn't created using ISAD
                    // template
                    $item->startDate = null;
                    $item->endDate = null;
                    $item->date = null;
                    $item->save();
                }
            }
        }
    }
}
