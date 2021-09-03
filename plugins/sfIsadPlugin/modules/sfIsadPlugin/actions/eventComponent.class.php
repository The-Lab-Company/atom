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

    /**
     * Get only date events for the ISAD template.
     */
    public function getEvents()
    {
        return $this->resource->getDates();
    }

    public function hasRequiredData($event)
    {
        if (
            empty($event['date']->getValue())
            && empty($event['endDate']->getValue())
            && empty($event['startDate']->getValue())
        ) {
            // Skip this row if there is no date data
            return false;
        }

        return true;
    }

    protected function deleteDeletedEvents()
    {
        // Delete the old events that were removed from the form by multiRow.js.
        foreach ($this->getEvents() as $event) {
            if (
                isset($event->id)
                && false === array_search($event->id, $this->finalEventIds)
            ) {
                // Will be indexed when description is saved
                $event->indexOnSave = false;

                if (!isset($item->actor)) {
                    // Only delete event if it has no associated actor
                    $event->delete();
                } else {
                    // ISAD events never have an actor, so keep this event but
                    // clear the date fields.
                    $event->startDate = null;
                    $event->endDate = null;
                    $event->date = null;
                    $event->save();
                }
            }
        }
    }
}
