@extends('layouts.app')

@section('content')
    <div class="container" id="calc">
        <h1>Конвертер валют</h1>

        @auth
            @if (count($orders)>0)
                <h3>Последние заказы:</h3>
                <div id="order_history">
                    @include('modules.order_history', ['orders' => $orders])
                </div>
        @endif
        @endauth

        <span>Введите сумму:</span><input type = "number" v-model.number = "amount" placeholder = "Введите сумму" />
        <br/><br/>

        <select v-model = "conv_from" class = "block-20-select">
            <option v-for = "(a, index) in currencyfrom"  v-bind:value = "a.iso_char_code">@{{a.name}}</option>
        </select>
        <span> => </span>
        <select v-model = "conv_to" class = "block-20-select">
            <option v-for = "(a, index) in currencyfrom" v-bind:value = "a.iso_char_code">@{{a.name}}</option>
        </select>
        <br/><br/>

        <button type="button" class="btn btn-primary" @click='calc()'>Посчитать</button>
        @auth
            <button type="button" class="btn btn-primary" @click='order()'>Обменять</button>
        @endauth
        <br/><br/>

        <span id="final"></span>

    </div>

    <script type = "text/javascript">
        var vm = new Vue({
            el: '#calc',
            data: {
                name:'',
                currencyfrom : currencies,
                conv_from: "USD",
                conv_to:"USD",
                amount :"0",
            },
            methods: {
                request: function(url) {
                    if (this.conv_to == this.conv_from) {
                        $('#final').text('Валюты совпадают, обмен невозможен.');
                        return;
                    }
                    $.getJSON(url, function(data) {
                        $('#final').text(data.msg);
                        $('#order_history').html(data.history);
                    });
                },
                calc: function() {
                    this.request('/convert?from=' + this.conv_from + '&to=' + this.conv_to + '&amount=' + this.amount);
                },
                order: function() {
                    this.request('/order?from=' + this.conv_from + '&to=' + this.conv_to + '&amount=' + this.amount);
                }
            }
        });
    </script>
    </div>
@endsection