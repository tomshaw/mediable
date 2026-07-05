<?php

namespace TomShaw\Mediable\Console\Commands;

use Illuminate\Support\Facades\Process;

trait BuildsAssets
{
    private function buildAssets(): void
    {
        Process::path(base_path())
            ->timeout(0)
            ->run(['npm', 'run', 'build'], function (string $type, string $output): void {
                if ($type === 'err') {
                    $this->error($output);
                } else {
                    $this->line($output);
                }
            })
            ->throw();
    }
}
