<?php

namespace Louis\Zenvia\Commands;

use Illuminate\Console\Command;
use Louis\Zenvia\Services\Zenvia;

class SendSmsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenvia:sms {number} {text=Teste Mensagem}';

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
     * @throws \Louis\Zenvia\Exceptions\AuthenticationNotFoundedException
     */
    public function handle()
    {
        try{
            $this->info('Iniciando envio de SMS para '. $this->argument('number'));
            $zenvia = new Zenvia(config('zenvia.account'), config('zenvia.password'));
            $zenvia->setNumber($this->argument('number'))
                ->setText($this->argument('text'))
                ->send();
            $this->info('SMS enviado para '. $this->argument('number'));
        }catch (\Exception $exception){
            $this->error('Erro: '.$exception->getMessage());
            $this->error('Code: '.$exception->getCode());

        }
    }
}
