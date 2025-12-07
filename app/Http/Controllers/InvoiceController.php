<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    //
    public function preview($company, $invoiceId,)
    {
        // Validasi input
        $invoice = Invoice::where('id', $invoiceId)->with('detailInvoices')->firstOrFail();
        return view('exports.tipe_invoice', compact('invoice'));
    }
}
