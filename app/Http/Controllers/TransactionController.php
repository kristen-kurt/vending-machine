<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\User;



class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin'){
            $transactions = Transaction::with(['user', 'product'])->paginate(10);
            // dd($transactions);
        }
        else
        {
            $transactions = $user->transactions()->with('product')->paginate(10);
        }
        
       
        return view('transactions.index', compact('transactions'));
    }
}
