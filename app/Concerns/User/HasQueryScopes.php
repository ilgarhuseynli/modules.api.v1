<?php

namespace App\Concerns\User;

use Illuminate\Database\Eloquent\Builder;

trait HasQueryScopes
{

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['name'] ?? null), fn ($q) => $q->where('name', 'like', '%' . $filters['name'] . '%'))
            ->when(filled($filters['keyword'] ?? null), fn ($q) => $q->where('keyword', 'like', '%' . $filters['keyword'] . '%'))
            ->when(filled($filters['type'] ?? null), fn ($q) => $q->where('type', $filters['type']))
            ->when(filled($filters['role_id'] ?? null), fn ($q) => $q->where('role_id', $filters['role_id']));
    }

}
