<?php

namespace App\Http\Services;

use App\Models\ListingRule;
use App\Services\LanguageService;
use App\Services\MessageService;

class ListingRuleService
{
    public function show($id)
    {
        $rule = ListingRule::find($id);

        if (!$rule) {
            MessageService::abort(404, 'messages.listing_rule.not_found');
        }

        return $rule;
    }

    public function create($data)
    {
        $data = LanguageService::prepareTranslatableData($data, new ListingRule());
        return ListingRule::create($data);
    }

    public function update(ListingRule $rule, array $data)
    {
        $data = LanguageService::prepareTranslatableData($data, $rule);
        $rule->update($data);
        return $rule;
    }

    public function destroy(ListingRule $rule)
    {
        $rule->delete();
    }
}
