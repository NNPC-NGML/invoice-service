<?php

namespace App\Jobs\CustomerSite;


use Illuminate\Bus\Queueable;
use Skillz\Nnpcreusable\Service\CustomerService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CustomerSiteDeleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {

        $service = new  CustomerService();
        $service->DestroyCustomerSite($this->id);
    }
}
