<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
class AuthController extends Controller {
    public function show() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }
    public function login(Request $request) { $data=$request->validate(['email'=>'required|email','password'=>'required']); if(!Auth::attempt($data,$request->boolean('remember'))) return back()->withErrors(['email'=>'Invalid credentials.'])->onlyInput('email'); $request->session()->regenerate(); return redirect()->route('admin.dashboard'); }
    public function register(Request $request) { $data=$request->validate(['name'=>'required|max:120','email'=>'required|email|unique:users','phone'=>'nullable|max:30','password'=>['required','confirmed',Password::min(8)]]); $data['is_admin']=false; Auth::login(User::create($data)); $request->session()->regenerate(); return redirect()->route('home'); }
    public function logout(Request $request) { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect('/'); }
    public function account(Request $request) { return view('auth.account',['orders'=>$request->user()->orders()->latest()->get(),'wishlist'=>$request->user()->wishlistProducts]); }
}
