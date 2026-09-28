<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentMethodRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::query()
            ->withCount('payments')
            ->orderBy('name')
            ->get();

        $pendingPerMethod = Payment::query()
            ->selectRaw('payment_method_id, count(*) as total')
            ->where('status', Payment::STATUS_PENDING)
            ->groupBy('payment_method_id')
            ->pluck('total', 'payment_method_id')
            ->all();

        return view('admin.payment-methods.index', compact('methods', 'pendingPerMethod'));
    }

    public function create(): View
    {
        return view('admin.payment-methods.create');
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $method = PaymentMethod::create([
            'name' => $request->string('name')->trim()->value(),
            'account_name' => $request->string('account_name')->trim()->value(),
            'account_number' => $request->string('account_number')->trim()->value(),
            'instructions' => $request->filled('instructions') ? $request->string('instructions')->trim()->value() : null,
            'status' => $request->string('status')->value(),
        ]);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Payment method "'.$method->name.'" created.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.edit', ['method' => $paymentMethod]);
    }

    public function update(StorePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update([
            'name' => $request->string('name')->trim()->value(),
            'account_name' => $request->string('account_name')->trim()->value(),
            'account_number' => $request->string('account_number')->trim()->value(),
            'instructions' => $request->filled('instructions') ? $request->string('instructions')->trim()->value() : null,
            'status' => $request->string('status')->value(),
        ]);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->payments()->exists()) {
            return back()->with('error', 'This method has payments attached. Deactivate it instead of deleting.');
        }

        $name = $paymentMethod->name;
        $paymentMethod->delete();

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Payment method '.$name.' deleted.');
    }
}
