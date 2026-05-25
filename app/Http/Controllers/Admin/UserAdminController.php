<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('utype', 'USR')
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate(10);
            
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function suspend(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'suspended']);
            return redirect()->route('admin.users.show', $user->id)->with('success', 'User suspended successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.show', $id)->with('error', 'Failed to suspend user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    // Optional: Add method to activate a suspended user
    public function activate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'active']);
            return redirect()->route('admin.users.show', $user->id)->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.show', $id)->with('error', 'Failed to activate user: ' . $e->getMessage());
        }
    }
}