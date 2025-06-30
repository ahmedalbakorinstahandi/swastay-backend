<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderHelper
{
    public static function assign(Model $model, string $orderField = 'orders'): void
    {
        $max = $model->newQuery()->withTrashed()->max($orderField) ?? 0;
        $model->{$orderField} = $max + 1;
        $model->save();
    }


    public static function reorder(Model $model, int $newOrder, string $orderField = 'orders'): void
    {
        $oldOrder = $model->{$orderField};

        if ($oldOrder === $newOrder) return;

        DB::transaction(function () use ($model, $oldOrder, $newOrder, $orderField) {
            $query = $model->newQuery()->withTrashed();

            if ($oldOrder < $newOrder) {
                $query->where($orderField, '>', $oldOrder)
                    ->where($orderField, '<=', $newOrder)
                    ->decrement($orderField);
            } else {
                $query->where($orderField, '>=', $newOrder)
                    ->where($orderField, '<', $oldOrder)
                    ->increment($orderField);
            }

            $model->{$orderField} = $newOrder;
            $model->save();
        });
    }
}
