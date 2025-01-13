@extends('layouts.app')

@section('content')
   <div class="container m-10">

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @if(count($transactions) > 0)
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->user->name }}</td>
                            <td>{{ $transaction->product->name }}</td>
                            <td>{{ $transaction->quantity }}</td>
                            <td>${{ number_format($transaction->total_amount, 2) }}</td>
                            <td>{{ $transaction->created_at }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="5" class="text-center">No purchases yet</td></tr>
                @endif
            </tbody>
        </table>
        {{ $transactions->links() }}
    </div>
@endsection