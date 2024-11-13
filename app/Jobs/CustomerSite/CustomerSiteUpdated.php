<?php

namespace App\Jobs\CustomerSite;


use Illuminate\Bus\Queueable;
use Skillz\Nnpcreusable\Service\CustomerService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CustomerSiteUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The data for updating the department.
     *
     * @var array
     */
    private array $data;

    /**
     * The ID of the department to be updated.
     *
     * @var int
     */
    private int $id;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param int $id
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'];
    }



    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {

        $service = new  CustomerService();
        $service->updateCustomerSite($this->data, $this->id);
    }
}
