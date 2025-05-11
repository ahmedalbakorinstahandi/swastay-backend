<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingRuleResource extends JsonResource
{
    public function toArray($request)
    {
        return array_filter([
            'id' => $this->id,
            'listing_id' => $this->listing_id,

            'allows_pets' => $this->whenNotNull($this->allows_pets),
            'allows_pets_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_pets, 'pets')),

            'allows_smoking' => $this->whenNotNull($this->allows_smoking),
            'allows_smoking_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_smoking, 'smoking')),

            'allows_parties' => $this->whenNotNull($this->allows_parties),
            'allows_parties_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_parties, 'parties')),

            'allows_children' => $this->whenNotNull($this->allows_children),
            'allows_children_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_children, 'children')),

            'remove_shoes' => $this->whenNotNull($this->remove_shoes),
            'remove_shoes_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->remove_shoes, 'shoes')),

            'no_extra_guests' => $this->whenNotNull($this->no_extra_guests),
            'no_extra_guests_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->no_extra_guests, 'extra_guests')),

            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,

            'quiet_hours' => $this->quiet_hours,
            'restricted_rooms_note' => $this->restricted_rooms_note,
            'garbage_disposal_note' => $this->garbage_disposal_note,
            'pool_usage_note' => $this->pool_usage_note,
            'forbidden_activities_note' => $this->forbidden_activities_note,
        ]);
    }

    protected function translateBooleanForAllLocales($value, $field)
    {
        if (!in_array($value, [true, false], true)) {
            return null;
        }

        $locales = config('translatable.locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = trans("rules_texts.{$field}." . ($value ? 'yes' : 'no'), [], $locale);
        }

        return $translations;
    }
}
