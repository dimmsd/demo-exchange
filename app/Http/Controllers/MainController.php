<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Order;
use App\Models\CurrencyHistory;
use App\Services\Sbr;
use Carbon\Carbon;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // для прода было бы логично убрать vue в компонент
        $user = \Auth::user();
        $orders = (!empty($user)) ? $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->take(5)->get() : [];
        $currencies = Currency::all()->toArray();
        \JavaScript::put(['currencies' => $currencies]);
        return view('main', compact('orders'));
    }

}
