<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserLocationController extends Controller
{
    /**
     * Display active users on map
     */
    public function index()
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        // Get active users with location data
        $users = User::where('business_id', $business_id)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->select('id', 'surname', 'first_name', 'last_name', 'latitude', 'longitude', 'location_updated_at')
                    ->get();
        
        // Get all users for filtering
        $all_users = User::where('business_id', $business_id)
                    ->select('id', 'surname', 'first_name', 'last_name')
                    ->get();
        
        return view('user.location_map')
             ->with(compact('users', 'all_users'));
    }

    /**
     * Update user's current location
     */
    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->location_updated_at = Carbon::now();
        $user->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * Get active users' locations
     */
    public function getActiveUsersLocations(Request $request)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $query = User::where('business_id', $business_id)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude');
        
        // Filter by specific users if provided
        if (!empty($request->input('users'))) {
            $query->whereIn('id', $request->input('users'));
        }
        
        // Only get users with recent location updates (within the last hour)
        $query->where('location_updated_at', '>=', Carbon::now()->subHour());
        
        $users = $query->select('id', 'surname', 'first_name', 'last_name', 'latitude', 'longitude', 'location_updated_at')
                    ->get();
        
        return response()->json($users);
    }
}