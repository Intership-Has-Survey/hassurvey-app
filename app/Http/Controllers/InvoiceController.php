<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoicesSetting;

class InvoiceController extends Controller
{
    //
    // public function preview($company, $invoiceId,)
    // {
    //     // Validasi input
    //     $invoice = Invoice::where('id', $invoiceId)->with('detailInvoices')->firstOrFail();
    //     return view('exports.tipe_invoice', compact('invoice'));
    // }

    public function preview($company, $invoiceId,)
    {
        // Validasi input
        $invoice = Invoice::where('id', $invoiceId)->with('detailInvoices')->firstOrFail();
        $invoiceSetting = InvoicesSetting::where('company_id', $company)->firstOrFail();
        return view('exports.tipe_invoice', compact('invoice', 'invoiceSetting'));
    }
}
