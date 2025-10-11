<?php

namespace App\Jobs;

use App\Support\HorizonTags;
use Illuminate\Support\Carbon;
use Override;
use Spatie\QueueableAction\ActionJob as BaseActionJob;

class ActionJob extends BaseActionJob
{
    public int $maxExceptions = 0;

    /**
     * @param  class-string|object  $action
     * @param  mixed[]  $parameters
     */
    public function __construct(string|object $action, array $parameters = [])
    {
        parent::__construct($action, $parameters);
    }

    #[Override]
    public function handle(): void
    {
        $action = app($this->actionClass);
        $action->job = $this->job;
        if (property_exists($action, 'actionJob')) {
            $action->actionJob = $this;
        }
        $action->{$action->queueMethod()}(...$this->parameters);
    }

    #[Override]
    public function retryUntil(): Carbon
    {
        return now()->addHours(6);
    }

    /**
     * @return string[]
     */
    #[Override]
    public function tags(): array
    {
        return array_unique(array_merge($this->tags, HorizonTags::for(
            ...$this->parameters()
        )));
    }
}
