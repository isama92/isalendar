<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * @implements Scope<Model>
 */
class ExcludeMarkedForDeletionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNull($this->getQualifiedMarkedForDeletionColumn($model));
    }

    /**
     * @param  Builder<Model>  $builder
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
