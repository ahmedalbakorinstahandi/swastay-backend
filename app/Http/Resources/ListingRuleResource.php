<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ListingRuleResource extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'listing_id' => $this->listing_id,

            'allows_pets' => $this->allows_pets,
            'allows_pets_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_pets, 'pets')),

            'allows_smoking' => $this->allows_smoking,
            'allows_smoking_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_smoking, 'smoking')),

            'allows_parties' => $this->allows_parties,
            'allows_parties_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_parties, 'parties')),

            'allows_children' => $this->allows_children,
            'allows_children_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->allows_children, 'children')),

            'remove_shoes' => $this->remove_shoes,
            'remove_shoes_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->remove_shoes, 'shoes')),

            'no_extra_guests' => $this->no_extra_guests,
            'no_extra_guests_text' => $this->whenNotNull($this->translateBooleanForAllLocales($this->no_extra_guests, 'extra_guests')),

            'check_in_time' => $this->check_in_time,
            'check_in_time_text' => $this->whenNotNull($this->check_in_time_description()),

            'check_out_time' => $this->check_out_time,
            'check_out_time_text' => $this->whenNotNull($this->check_out_time_description()),

            'quiet_hours' => $this->quiet_hours,
            'restricted_rooms_note' => $this->restricted_rooms_note,
            'garbage_disposal_note' => $this->garbage_disposal_note,
            'pool_usage_note' => $this->pool_usage_note,
            'forbidden_activities_note' => $this->forbidden_activities_note,
        ];
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

    protected function check_in_time_description()
    {
        if (!$this->check_in_time) return null;

        $locales = config('translatable.locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = trans('rules_texts.check_in_time', [
                'time' => Carbon::parse($this->check_in_time)->format('g:i A'),
            ], $locale);
        }

        return $translations;
    }

    protected function check_out_time_description()
    {
        if (!$this->check_out_time) return null;

        $locales = config('translatable.locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = trans('rules_texts.check_out_time', [
                'time' => Carbon::parse($this->check_out_time)->format('g:i A'),
            ], $locale);
        }

        return $translations;
    }
}
