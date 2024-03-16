<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::orderBy('created_at', 'desc')->get();
        return response([
            'message' => 'Transactions fetched successfully',
            'transactions' => $transactions
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
            "status" => "in:processing,pending,completed,failed",
            "payment_method" => "in:credit_card,debit_card,paypal,others",
        ]);

        $transaction = Transaction::create([
            "booking_id" => $request->booking_id,
            "status" => $request->status,
            "payment_method" => $request->payment_method,
        ]);

        return response([
            'message' => 'Transaction created successfully',
            'transaction' => $transaction
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "booking_id" => "required",
            "status" => "in:processing,pending,completed,failed",
            "payment_method" => "in:credit_card,debit_card,paypal,others",
        ]);

        $transaction = Transaction::where('id', $id)->first();

        $transaction->update([
            "booking_id" => $request->booking_id,
            "status" => $request->status,
            "payment_method" => $request->payment_method,
        ]);

        return response([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ], 200);
    }

    public function show($id)
    {
        $transaction = Transaction::where('id', $id)->first();
        return response([
            'message' => 'Transaction fetched successfully',
            'transaction' => $transaction
        ], 200);
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('id', $id)->first();
        $transaction->delete();
        return response([
            'message' => 'Transaction deleted successfully',
        ], 200);
    }
}
