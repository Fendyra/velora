<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCustomers = User::where('utype', 'USR')->count();
        
        $monthlyRevenue = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'selesai') 
            ->sum('total_amount');

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();
            
        $lowStockProducts = Product::with('category')->where('stock', '<', 5)->orderBy('stock', 'asc')->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalOrders', 'totalCustomers', 'monthlyRevenue', 'recentOrders', 'lowStockProducts'
        ));
    }

    /**
     * Menyediakan data penjualan untuk chart berdasarkan periode yang dipilih.
     */
    public function getSalesData(Request $request)
    {
        $period = $request->input('period', 'last_30_days');
        $now = Carbon::now();
        
        $labels = [];
        $data = [];

        switch ($period) {
            case 'last_7_days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                
                // Generate labels for last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $labels[] = $date->format('M d');
                }
                
                // Get sales data
                $salesData = Order::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total_amount) as total')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('total', 'date')
                    ->toArray();
                
                // Fill data array
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i)->format('Y-m-d');
                    $data[] = $salesData[$date] ?? 0;
                }
                break;
                
            case 'last_6_months':
                $startDate = $now->copy()->subMonths(5)->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                
                // Generate labels for last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $labels[] = $date->format('M Y');
                }
                
                // Get sales data
                $salesData = Order::select(
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                        DB::raw('SUM(total_amount) as total')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
                
                // Fill data array
                for ($i = 5; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i)->format('Y-m');
                    $data[] = $salesData[$month] ?? 0;
                }
                break;
                
            case 'last_12_months':
                $startDate = $now->copy()->subMonths(11)->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                
                // Generate labels for last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $labels[] = $date->format('M Y');
                }
                
                // Get sales data
                $salesData = Order::select(
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                        DB::raw('SUM(total_amount) as total')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
                
                // Fill data array
                for ($i = 11; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i)->format('Y-m');
                    $data[] = $salesData[$month] ?? 0;
                }
                break;
                
            case 'last_30_days':
            default:
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                
                // Generate labels for last 30 days (show every 5th day to avoid crowding)
                for ($i = 29; $i >= 0; $i -= 5) {
                    $date = $now->copy()->subDays($i);
                    $labels[] = $date->format('M d');
                }
                
                // Get sales data
                $salesData = Order::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total_amount) as total')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('total', 'date')
                    ->toArray();
                
                // Fill data array (aggregate data for 5-day periods)
                for ($i = 29; $i >= 0; $i -= 5) {
                    $total = 0;
                    for ($j = 0; $j < 5 && ($i - $j) >= 0; $j++) {
                        $date = $now->copy()->subDays($i - $j)->format('Y-m-d');
                        $total += $salesData[$date] ?? 0;
                    }
                    $data[] = $total;
                }
                break;
        }
        
        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'period' => $period
        ]);
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:15',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->filled('telephone')) {
            $user->telephone = $request->telephone;
        }
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
}