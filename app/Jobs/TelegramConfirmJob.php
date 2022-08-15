<?php

namespace App\Jobs;

use App\Monitoring\TelegramComponent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram;

class TelegramConfirmJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tmp = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        TelegramComponent::confirmProcess();
    }

    /**
     * The unique ID of the job.
     *
     * only one job in work
     * @return string
     */
    public function uniqueId()
    {
        return $this->tmp;
    }

}
