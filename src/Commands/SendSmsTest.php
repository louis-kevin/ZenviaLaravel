<?php

namespace Louis\Zenvia\Commands;

use Illuminate\Console\Command;

class SendSmsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:sms {number=5541997703592} {text=Teste Mensagem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia um sms de teste para o numero passado';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
