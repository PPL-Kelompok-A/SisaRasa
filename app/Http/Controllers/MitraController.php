<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\Ulasan; // <--- 1. TAMBAHKAN INI untuk memanggil Model Ulasan
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MitraController extends Controller
{
    // ... (SEMUA METHOD LAMA DARI public function dashboard() SAMPAI public function orderHistoryShow() TETAP SAMA DAN TIDAK DIUBAH) ...
    
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ensure only mitra users can access this page
        if (!$user || $user->role !== 'mitra') {
            return redirect()->route('login');
        }
        
        // Debug user information
        Log::info('User ID: ' . $user->id);
        Log::info('User Role: ' . $user->role);
        
        $orders = Order::where('mitra_id', $user->id)
            ->with(['items.food', 'user'])
            ->latest()
            ->take(5)
            ->get();
            
        // Debug orders
        Log::info('Orders count: ' . $orders->count());

        // Analytics data
        // Set timezone to Asia/Jakarta for Indonesia
        $today = Carbon::today('Asia/Jakarta');
        $last7Days = Carbon::now('Asia/Jakarta')->subDays(7);
        $startOfMonth = Carbon::now('Asia/Jakarta')->startOfMonth();
        $startOfYear = Carbon::now('Asia/Jakarta')->startOfYear();

        // Daily sales with explicit date range using OrderHistory
        $startOfToday = $today->copy()->startOfDay();
        $endOfToday = $today->copy()->endOfDay();
        
        // Get orders completed today using OrderHistory
        $dailySales = OrderHistory::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfToday, $endOfToday])
            ->sum('total_amount');
            
        // For debugging
        $todayCompletedOrders = OrderHistory::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfToday, $endOfToday])
            ->get();
            
        Log::info('Today date range: ' . $startOfToday . ' to ' . $endOfToday);
        Log::info('Today completed orders count: ' . $todayCompletedOrders->count());
        foreach ($todayCompletedOrders as $order) {
            Log::info('Order ID: ' . $order->order_id . ', Amount: ' . $order->total_amount . ', Completed at: ' . $order->completed_at);
        }

        // Weekly sales (last 7 days) using OrderHistory
        $weeklySales = OrderHistory::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$last7Days, $endOfToday])
            ->sum('total_amount');

        // Yearly sales using OrderHistory
        $yearlySales = OrderHistory::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfYear, $endOfToday])
            ->sum('total_amount');

        // Most popular foods
        $popularFoods = Food::where('user_id', $user->id)
            ->withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
            
        // Debug foods
        Log::info('Foods count: ' . $popularFoods->count());
        foreach ($popularFoods as $food) {
            Log::info('Food: ' . $food->name . ', Total sold: ' . $food->total_sold);
        }
        
        // Get foods on flash sale
        $flashSaleItems = Food::where('user_id', $user->id)
            ->where('on_flash_sale', true)
            ->whereRaw('(flash_sale_starts_at IS NULL OR flash_sale_starts_at <= NOW())')
            ->whereRaw('(flash_sale_ends_at IS NULL OR flash_sale_ends_at >= NOW())')
            ->latest()
            ->get();

        return view('mitra.dashboard', compact(
            'user',
            'orders',
            'dailySales',
            'weeklySales',
            'yearlySales',
            'popularFoods',
            'flashSaleItems'
        ));
    }

    public function index()
    {
        $user = Auth::user();
        $foods = Food::where('user_id', $user->id)->get();
        $totalFoods = $foods->count();
        $availableFoods = $foods->where('is_available', true)->count();
        
        // Get the count of orders by status
        $pendingOrders = Order::where('mitra_id', $user->id)
            ->where('status', 'pending')
            ->count();
            
        $processingOrders = Order::where('mitra_id', $user->id)
            ->where('status', 'processing')
            ->count();
            
        // Get completed and cancelled orders from order history
        $completedOrders = OrderHistory::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
            
        $cancelledOrders = OrderHistory::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->count();

        return view('mitra.dashboard', compact(
            'user',
            'totalFoods',
            'availableFoods',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders'
        ));
    }

    public function foods()
    {
        $foods = Auth::user()->foods()->latest()->paginate(10);
        return view('mitra.foods.index', compact('foods'));
    }

    public function createFood()
    {
        return view('mitra.foods.create');
    }

    public function storeFood(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        // Set default availability to true if not provided
        $validated['is_available'] = $request->has('is_available') ? true : false;

        if ($request->hasFile('image')) {
            // Simpan file ke storage/app/public/foods
            $path = $request->file('image')->store('foods', 'public');
            $validated['image'] = $path;
        }

        $validated['user_id'] = Auth::id();
        Food::create($validated);

        return redirect()->route('mitra.foods.index')
            ->with('success', 'Food item created successfully');
    }

    public function editFood(Food $food)
    {
        // Check if the authenticated user is the owner of this food item
        if (Auth::id() !== $food->user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('mitra.foods.edit', compact('food'));
    }

    public function updateFood(Request $request, Food $food)
    {
        // Check if the authenticated user is the owner of this food item
        if (Auth::id() !== $food->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        // Set availability based on checkbox
        $validated['is_available'] = $request->has('is_available') ? true : false;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            
            // Store the new image
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }

        $food->update($validated);

        return redirect()->route('mitra.foods.index')
            ->with('success', 'Food item updated successfully');
    }

    public function destroyFood(Food $food)
    {
        // Check if the authenticated user is the owner of this food item
        if (Auth::id() !== $food->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the image file if it exists
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }

        $food->delete();

        return redirect()->route('mitra.foods.index')
            ->with('success', 'Food item deleted successfully');
    }
    
    /**
     * Show the flash sale management page
     */
    public function flashSaleIndex()
    {
        $foods = Auth::user()->foods()->latest()->paginate(10);
        return view('mitra.foods.flash-sale', compact('foods'));
    }
    
    /**
     * Show the form for creating a flash sale for a specific food
     */
    public function createFlashSale(Food $food)
    {
        // Check if the authenticated user is the owner of this food item
        if (Auth::id() !== $food->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('mitra.foods.flash-sale-form', compact('food'));
    }
    
    /**
     * Store a flash sale for a specific food
     */
    public function storeFlashSale(Request $request, Food $food)
    {
        // Check if the authenticated user is the owner of this food item
        if (Auth::id() !== $food->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'flash_sale_starts_at' => 'nullable|date',
            'flash_sale_ends_at' => 'nullable|date|after_or_equal:flash_sale_starts_at',
        ]);
        
        // Set the flash sale data
        $food->on_flash_sale = true;
        
        if ($validated['discount_type'] === 'fixed') {
            $food->discount_price = $validated['discount_value'];
            $food->discount_percentage = null;
        } else {
            $food->discount_price = null;
            $food->discount_percentage = $validated['discount_value'];
        }
        
        if ($request->filled('flash_sale_starts_at')) {
            $food->flash_sale_starts_at = $validated['flash_sale_starts_at'];
        }
        
        if ($request->filled('flash_sale_ends_at')) {
            $food->flash_sale_ends_at = $validated['flash_sale_ends_at'];
        }
        
        $food->save();
        
        return redirect()->route('mitra.foods.flash-sale.index')
            ->with('success', 'Flash sale created successfully');
    }
    
    /**
     * Remove a flash sale from a food item
     */
    public function removeFlashSale(Food $food)
    {
        // Check if the authenticated user is the owner of this food item
        if (Auth::id() !== $food->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $food->update([
            'on_flash_sale' => false,
            'discount_price' => null,
            'discount_percentage' => null,
            'flash_sale_starts_at' => null,
            'flash_sale_ends_at' => null,
        ]);
        
        return redirect()->route('mitra.foods.flash-sale.index')
            ->with('success', 'Flash sale removed successfully');
    }

    public function ordersIndex()
    {
        // Only show pending and processing orders
        $orders = Order::with(['items.food', 'user'])
            ->where('mitra_id', Auth::id())
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->paginate(10);

        return view('mitra.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Order $order, Request $request)
    {
        // Check if the authenticated user is the mitra for this order
        if (Auth::id() !== $order->mitra_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];
        
        // Update the order status
        $order->update($validated);
        
        // If the order is completed or cancelled, move it to order history
        if ($newStatus === 'completed' || $newStatus === 'cancelled') {
            // Calculate total amount
            $totalAmount = 0;
            $orderItems = [];
            
            foreach ($order->items as $item) {
                $totalAmount += $item->quantity * $item->price;
                $orderItems[] = [
                    'food_id' => $item->food_id,
                    'food_name' => $item->food->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ];
            }
            
            // Create order history record
            OrderHistory::create([
                'user_id' => $order->mitra_id,
                'order_id' => $order->id,
                'order_number' => 'ORD-' . $order->id, // Generate order number from ID
                'total_amount' => $totalAmount,
                'status' => $newStatus,
                'order_items' => $orderItems,
                'completed_at' => now()
            ]);
            
            // If the order is completed, update the mitra's balance
            if ($newStatus === 'completed') {
                $mitra = User::find($order->mitra_id);
                
                // Debug information
                Log::info('Updating mitra balance');
                Log::info('Mitra ID: ' . $mitra->id);
                Log::info('Old balance: ' . $mitra->balance);
                Log::info('Order total: ' . $totalAmount);
                
                // Make sure we're working with numeric values
                $currentBalance = floatval($mitra->balance);
                $orderTotal = floatval($totalAmount);
                
                // Update the balance
                $mitra->balance = $currentBalance + $orderTotal;
                
                Log::info('New balance: ' . $mitra->balance);
                
                // Save the updated balance
                $mitra->save();
            }
        }

        return redirect()->route('mitra.orders.index')
            ->with('success', 'Order status updated successfully');
    }
    
    public function orderHistoryIndex()
    {
        $user = auth()->user();
        $orderHistories = OrderHistory::where('user_id', $user->id)
            ->with('ulasan')
            ->orderBy('completed_at', 'desc')
            ->paginate(10);
            
        return view('mitra.orders.history.index', compact('orderHistories'));
    }
    
    /**
     * Show order history details
     */
    public function orderHistoryShow(OrderHistory $orderHistory)
    {
        // Check if the order history belongs to the authenticated user
        if ($orderHistory->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Format order items for display
        $formattedItems = [];
        foreach ($orderHistory->order_items as $item) {
            $formattedItems[] = [
                'name' => $item['name'] ?? 'Unknown Item',
                'quantity' => $item['quantity'] ?? 0,
                'price' => $item['price'] ?? 0,
                'subtotal' => $item['subtotal'] ?? 0,
            ];
        }

        return response()->json([
            'id' => $orderHistory->id,
            'order_number' => $orderHistory->order_number,
            'total_amount' => number_format($orderHistory->total_amount, 0, ',', '.'),
            'status' => ucfirst($orderHistory->status),
            'completed_at' => $orderHistory->completed_at->format('M d, Y H:i'),
            'items' => $formattedItems,
            'customer_name' => $orderHistory->user->name ?? 'Unknown Customer',
            'delivery_address' => $orderHistory->delivery_address ?? 'No address provided'
        ]);
    }
    public function semuaUlasan()
    {
        // 1. Dapatkan data mitra yang sedang login
        $mitra = Auth::user();

        // 2. Ambil semua ulasan yang terhubung ke pesanan milik mitra ini.
        $ulasan = Ulasan::whereHas('order', function($query) use ($mitra) {
            $query->where('mitra_id', $mitra->id);
        })
        ->with(['user', 'order.items.food']) // Ambil juga data user & produk
        ->latest() // Urutkan dari yang terbaru
        ->paginate(15); // Tampilkan 15 ulasan per halaman

        return view('mitra.ulasan', compact('ulasan'));
    }
}