<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExcludeMarkedForDeletionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNull($this->getQualifiedMarkedForDeletionColumn($model));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withMarkedForDeletion', fn (Builder $builder) => $builder->withoutGlobalScope(self::class));
    }

    private function getQualifiedMarkedForDeletionColumn(Model $model): string
    {
        return $model->qualifyColumn('marked_for_deletion_at');
    }
}
