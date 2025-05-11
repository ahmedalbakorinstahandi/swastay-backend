<?php

namespace App\Http\Services;

use App\Http\Permissions\CategoryPermission;
use App\Models\Category;
use App\Models\User;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function index($filters = [])
    {
        $query = Category::query();

        $query = CategoryPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $filters,
            ['name', 'description'],
            [],
            [],
            ['key', 'is_visible'],
            ['key']
        );
    }


    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            MessageService::abort(404, 'messages.category.not_found');
        }

        return $category;
    }

    public function create($data)
    {

        $data = LanguageService::prepareTranslatableData($data, new Category);

        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        $data = LanguageService::prepareTranslatableData($data, $category);

        $category->update($data);
        return $category;
    }

    public function destroy(Category $category)
    {

        $category->delete();
    }
}
