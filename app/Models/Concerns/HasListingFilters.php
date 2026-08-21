<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasListingFilters
{
    protected function applySearch(Builder $query, ?string $search, array $columns): Builder
    {
        if (blank($search)) {
            return $query;
        }

        $term = '%' . trim($search) . '%';

        return $query->where(function (Builder $q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', $term);
            }
        });
    }

    protected function applyExactFilter(Builder $query, string $column, ?string $value): Builder
    {
        if (filled($value)) {
            $query->where($column, $value);
        }

        return $query;
    }

    protected function applyDateRange(Builder $query, string $column, ?string $from, ?string $to): Builder
    {
        if (filled($from)) {
            $query->whereDate($column, '>=', $from);
        }

        if (filled($to)) {
            $query->whereDate($column, '<=', $to);
        }

        return $query;
    }
}
