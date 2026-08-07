<?php
namespace App\Http\Controllers;
use App\Models\{AuditLog, Property};
class AdminController extends Controller {
    public function dashboard() { return view('admin.dashboard',['propertyCount'=>Property::count(),'publishedCount'=>Property::where('is_published',true)->count(),'rentalCount'=>Property::where('listing_type','rent')->count(),'saleCount'=>Property::where('listing_type','sale')->count()]); }
    public function settings() { return view('admin.settings',['logs'=>AuditLog::latest()->take(25)->get()]); }
}
