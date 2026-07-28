<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope to automatically filter data by posyandu_id
 * for users with 'posyandu' role.
 *
 * - Posyandu users: only see data belonging to their posyandu.
 * - Kelurahan users: see all data (no filter applied).
 */
class PosyanduScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user && $user->role === 'posyandu' && $user->posyandu_id) {
            $builder->where($model->getTable() . '.posyandu_id', $user->posyandu_id);
        }
    }
}
