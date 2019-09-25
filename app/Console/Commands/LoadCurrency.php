<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use App\Services\Sbr;

class LoadCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:load-currency-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузка списка валют';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function check_fields($currency) {
        $check_false = ($currency['code'] !== false &&
            $currency['name'] !== false &&
            $currency['iso_num_code'] !== false &&
            $currency['iso_char_code'] !== false &&
            $currency['nominal'] !== false);
        $check_empty = (!empty($currency['code']) &&
            !empty($currency['name']) &&
            !empty($currency['iso_num_code']) &&
            !empty($currency['iso_char_code']) &&
            !empty($currency['nominal']));
        return $check_empty && $check_false;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sbr = new Sbr();
        $list = $sbr->get_currency_list();
        if ($list !== false) {
            foreach ($list as $currency) {
                $curr = Currency::where('code', $currency['code'])->first();
                if (empty($curr)) {
                    if ($this->check_fields($currency)) {
                        $curr = Currency::create($currency);
                        $this->info('Добавлена валюта ' . $curr->name);
                    } else {
                        $this->error('Валюта ' . $currency['name'] . ' - некорректные поля');
                    }
                } else {
                    $this->info('Валюта ' . $curr->name . ' уже существует');
                }
            }
        } else {
            $this->error('Ошибка загрузки данных');
        }
    }
}
