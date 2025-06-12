<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class PaymentController extends Controller
{
    public function showPaymentPage(Request $request)
    {
        $order = null;
        $orderItems = collect();

        // Jika ada order_id dari checkout (query parameter atau session)
        $orderId = $request->get('order_id') ?? session('order_id');

        if ($orderId) {
            $order = Order::with(['items.food', 'mitra'])->findOrFail($orderId);

            // Pastikan order milik user yang login
            if ($order->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this order.');
            }

            $orderItems = $order->items;
        }

        return view('payment.index', compact('order', 'orderItems'));
    }

    public function getPaymentDetails($methodId)
    {
        // Ambil detail metode pembayaran berdasarkan $methodId
        // Contoh dummy data
        $details = [
            'id' => $methodId,
            'name' => 'Metode Pembayaran Contoh',
            'desc' => 'Deskripsi metode pembayaran.'
        ];
        return response()->json($details);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:DANA,BCA,ShopeePay',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        // Jika ada order_id, update status order
        if ($request->has('order_id')) {
            $order = Order::findOrFail($request->order_id);

            // Pastikan order milik user yang login
            if ($order->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this order.');
            }

            // Update status order
            $oldStatus = $order->status;
            $order->status = 'processing';
            $order->save();

            // Create notification for payment processed
            NotificationService::paymentProcessed($order);
        }

        // Proses pembayaran di sini
        // Validasi dan logika pembayaran bisa ditambahkan sesuai kebutuhan

        // Jika ada order_id, redirect ke Chatify dengan mitra
        if ($request->has('order_id')) {
            $order = Order::findOrFail($request->order_id);

            // Redirect ke Chatify dengan mitra_id dan order_id untuk upload bukti pembayaran
            return redirect("/chatify/{$order->mitra_id}?order_id={$order->id}")
                ->with('success', 'Pembayaran berhasil diproses! Silakan upload bukti pembayaran dan chat dengan mitra.');
        }

        return redirect('/chatify')->with('success', 'Pembayaran berhasil diproses! Silakan chat dengan mitra untuk konfirmasi.');
    }
    public function confirmPayment(Request $request)
    {
    $request->validate([
        'order_id' => 'required|integer|exists:orders,id',
    ]);

    $order = \App\Models\Order::findOrFail($request->order_id);

    if ($order->status === 'pending') {
        $oldStatus = $order->status;
        $order->status = 'processing';
        $order->save();

        // Create notification for payment processed
        NotificationService::paymentProcessed($order);

        // Redirect ke chatify dengan mitra untuk konfirmasi
        return redirect("/chatify/{$order->mitra_id}?order_id={$order->id}")
            ->with('success', 'Pembayaran berhasil diproses! Silakan upload bukti pembayaran dan chat dengan mitra.');

    }

     return redirect()->route('payment.show')->with('error', 'Status pesanan tidak valid atau sudah diproses.');
}
}
