<?php

namespace App\Http\Controllers;

use App;
use Carbon\Carbon;
use App\Models\Currency;
use App\Models\Order;
use App\Models\CurrencyHistory;
use Illuminate\Http\Request;
use App\Services\Sbr;

class ExchangeController extends Controller
{
    protected $error_message = '';

    protected $from_currency = null;

    protected $to_currency = null;

    protected $rate_to_value = 0;

    protected $rate_from_value = 0;

    protected function check($amount, $conv_from, $conv_to) {
        if (filter_var($amount, FILTER_VALIDATE_INT) === false || $amount<=0) {
            $this->error_message = 'Некорректная сумма';
            return false;
        }
        $this->from_currency = Currency::where('iso_char_code', $conv_from)->first();
        if (empty($this->from_currency)) {
            $this->error_message = 'Валюта ' . $conv_from . 'не найдена';
            return false;
        }
        $this->to_currency = Currency::where('iso_char_code', $conv_to)->first();
        if (empty($this->to_currency)) {
            $this->error_message = 'Валюта ' . $conv_to . 'не найдена';
            return false;
        }
    }

    protected function calc_rate($amount) {
        try {
            $sbr = new Sbr();
            // Данные обновляются 1 раз в день?

            $rate_from = CurrencyHistory::where(['currency_code' => $this->from_currency->code, 'request_at' => Carbon::now()])->first();
            if (!empty($rate_from)) {
                $this->rate_from_value = $rate_from->value;
            } else {
                $this->rate_from_value = str_replace(',', '.', $sbr->get_currency_rate($this->from_currency->code));
                if (is_numeric($this->rate_from_value)) {
                    //save history
                    CurrencyHistory::create(['currency_code' => $this->from_currency->code, 'request_at' => Carbon::now(), 'value' => $this->rate_from_value]);
                } else {
                    \Log::error(sprintf('Exchange from %s - rate %s', $this->from_currency->code, $this->rate_from_value));
                    $this->error_message = 'Не удалось получить курс.';
                    return false;
                }
            }

            $rate_to = CurrencyHistory::where(['currency_code' => $this->to_currency->code, 'request_at' => Carbon::now()])->first();
            if (!empty($rate_to)) {
                $this->rate_to_value = $rate_to->value;
            } else {
                $this->rate_to_value = str_replace(',', '.', $sbr->get_currency_rate($this->to_currency->code));
                if (is_numeric($this->rate_to_value)) {
                    //save history
                    CurrencyHistory::create(['currency_code' => $this->to_currency->code, 'request_at' => Carbon::now(), 'value' => $this->rate_to_value]);
                } else {
                    \Log::error(sprintf('Exchange to %s - rate %s', $this->to_currency->code, $this->rate_to_value));
                    $this->error_message = 'Не удалось получить курс.';
                    return false;
                }
            }
            //\Log::debug(sprintf('From %s | %s',$this->rate_from_value, $this->from_currency->nominal));
            //\Log::debug(sprintf('To %s | %s',$this->rate_to_value, $this->to_currency->nominal));
            $result = ($amount * $this->rate_from_value * $this->to_currency->nominal) / ($this->rate_to_value * $this->from_currency->nominal);
        } catch (\Exception $e) {
            $this->error_message = $e->getMessage();
            return false;
        }
        // Ну как вариант....
        return round($result);
    }

    /**
     * Convert.
     *
     * @return \Illuminate\Http\Response
     */
    public function convert(Request $request)
    {
        if (!$request->ajax()) {
            return App::Abort(404);
        }
        $conv_from = $request->input('from');
        $conv_to = $request->input('to');
        $amount = $request->input('amount');
        if ($this->check($amount, $conv_from, $conv_to) === false) {
            return response()->json(['status' => 'Ok', 'msg' => $this->error_message], 200);
        }
        $summ = $this->calc_rate($amount);
        if ($summ === false) {
            return response()->json(['status' => 'Ok', 'msg' => $this->error_message], 200);
        }
        $message = sprintf('Итоговая сумма %s %s', $summ, $conv_to);
        return response()->json(['status' => 'OK', 'msg' => $message], 200);
    }

    /**
     * Order.
     *
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request)
    {
        $user = \Auth::user();
        if (!$request->ajax() || empty($user)) {
            return App::Abort(404);
        }
        $conv_from = $request->input('from');
        $conv_to = $request->input('to');
        $amount = $request->input('amount');
        // Вообще валидацию можно сделать и через FormRequest, но вместо одного метода будет целый класс...
        if ($this->check($amount, $conv_from, $conv_to) === false) {
            return response()->json(['status' => 'Ok', 'msg' => $this->error_message], 200);
        }
        $summ = $this->calc_rate($amount);
        if ($summ === false) {
            return response()->json(['status' => 'Ok', 'msg' => $this->error_message], 200);
        }
        if ($summ === 0) {
            // При слишком большом номинале это возможно?
            // ToDo: проверить при большой разнице номиналов....
            return response()->json(['status' => 'Ok', 'msg' => 'Не удается обменять - сумма слишком мала'], 200);
        }
        $message = sprintf('Обмен завершен, получено %s %s', $summ, $conv_to);
        // save order
        Order::create([
            'currency_code_from' => $this->from_currency->code,
            'currency_code_to' => $this->to_currency->code,
            'user_id' => $user->id,
            'summa' => $amount,
            'value_from' => $this->rate_from_value,
            'value_to' => $this->rate_to_value
        ]);
        // Отправка почты в очередь
        // Рендерим шаблон с историей
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->take(5)->get();
        $view = view('modules.order_history', compact('orders'));
        $history = $view->render();
        return response()->json(['status' => 'OK', 'msg' => $message, 'history' => $history], 200);
    }

}
