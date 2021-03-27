<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogHistory;

class daily extends Command
{
    private $log;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:verifyBillet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email billet last day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LogHistory $log)
    {
        parent::__construct();
        $this->log = $log;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** Cria log */
        $log = [
            'name' => 'Test CRON', 'description' => "Test CRON run: " . date('d/m/Y'), 'type' => 'success', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => 0
        ];
        $this->log->createLog($log);
        echo "Run now - " . date('d/m/Y H:i:s');

    }
}
