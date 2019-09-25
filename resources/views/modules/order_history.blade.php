<div class="table-responsive-sm">
    <table class="table">
        <thead>
            <tr>
                <th>Время</th>
                <th>Валюта исходная</th>
                <th>Валюта целевая</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <th>{{ $order->created_at }}</th>
                    <th>{{ $order->currency_from->iso_char_code }}</th>
                    <th>{{ $order->currency_to->iso_char_code }}</th>
                    <th>{{ round($order->summa, 2)}}</th>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>