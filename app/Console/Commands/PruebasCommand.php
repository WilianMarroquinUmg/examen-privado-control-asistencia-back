<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PruebasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pruebas:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $user = User::with('fotoCertificada')
        ->find(38);

        dd($user->toArray());


        dd($user->getMedia('avatars')->last()->getUrl('thumb24'));

    }
}
