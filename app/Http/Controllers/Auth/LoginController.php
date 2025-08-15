<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $field => $login,
            'password' => $request->input('password')
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
          $this->redirectBasedOnRole($user);
            // توجيه المستخدم بناءً على نوع حسابه
            if ($user->role=='student') {
                return redirect()->route('student.dashboard');
            } 
            else 
             {
              
                return redirect()->route('admin.dashboard');
            } 

            return redirect('/');
        }

        throw ValidationException::withMessages([
            'login' => ['بيانات تسجيل الدخول غير صحيحة.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect the user based on their role.
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->role=='student') {
            return redirect()->route('student.dashboard');
        } else 
         {
            return redirect()->route('admin.dashboard');
        } 

        return redirect('/');
    }
}
