<?php

namespace Database\Seeders;

use Illuminate\Console\View\Components\TwoColumnDetail;

trait HasSeedComments
{
    private function seed(string $name, callable $callable): mixed
    {
        (new TwoColumnDetail($this->command->getOutput()))->render(
            $name,
            '<fg=yellow;options=bold>RUNNING</>'
        );

        $startTime = microtime(true);

        $return = $callable();

        $runTime = number_format((microtime(true) - $startTime) * 1000, 2);

        (new TwoColumnDetail($this->command->getOutput()))->render(
            $name,
            "<fg=gray>$runTime ms</> <fg=green;options=bold>DONE</>"
        );

        $this->command->getOutput()->writeln('');

        return $return;
    }
}
