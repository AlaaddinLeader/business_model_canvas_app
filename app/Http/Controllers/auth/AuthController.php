<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegisterForm(){
        return view('auth.register');
    }
    /**
     * Handle user registration with secure validation
     */
    public function register(Request $request){
        // Rate limiting for registration attempts
        $key = 'register-' . $request->ip();

        if(RateLimiter::tooManyAttempts($key, 5)){
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        // Validate input with strict rules
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{Arabic}a-zA-Z\s]+$/u'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                'not_regex:/[<>"\']/' // Prevent XSS attempts
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ],[
        'name.required' => 'الاسم مطلوب.',
        'name.regex' => 'الاسم يمكن أن يحتوي فقط على حروف ومسافات.',
        'email.required' => 'البريد الإلكتروني مطلوب.',
        'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
        'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
        'password.required' => 'كلمة المرور مطلوبة.',
        'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف.',
        'password.letters' => 'كلمة المرور يجب أن تحتوي على حروف.',
        'password.mixedCase' => 'كلمة المرور يجب أن تحتوي على حروف كبيرة وصغيرة.',
        'password.numbers' => 'كلمة المرور يجب أن تحتوي على أرقام.',
        'password.symbols' => 'كلمة المرور يجب أن تحتوي على رموز خاصة.',
        ]);

        RateLimiter::hit($key, 300); // 5 minutes penalty

        try{
            // Create user with hashed password
            $user = User::create([
                'name' => strip_tags($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'password' => Hash::make($validated['password']),
            ]);

            // Log the user in
            Auth::login($user);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Clear rate limiter
            RateLimiter::clear($key);

            // Redirect to dashboard
            return redirect()->route('dashboard')
                ->with('success', 'تم إنشاء الحساب بنجاح! مرحباً بك');
        } catch (\Exception $e){
            return back()->withInput($request->except('password','password_confirmation'))
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.']);
        }
    }
    /**
     * Show the login form
     */
    public function showLoginForm(){
        return view('auth.login');
    }

    /**
     * Handle user login with security measures
     */
    public function login(Request $request){
        // Rate limiting for login attempts
        $key = 'login-' . $request->ip();

        if(RateLimiter::tooManyAttempts($key, 5)){
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        // Validate credentials
        $credentials = $request->validate([
            'email' => ['required', 'email', 'not_regex:/[<>"\']/'],
            'password' => ['required', 'string'],
        ],[
            'email.required' => ' البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        // Sanitize email
        $credentials['email'] = strtolower(trim($credentials['email']));

        // Attempt authentication
        if(Auth::attempt($credentials, $request->boolean('remember'))){
            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Log successful login
            Log::info('User logged in', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'مرحباً بعودتك!');
        }

        // Increment rete limiter on failed attempt
        RateLimiter::hit($key, 60);

        // Log failed login attempt
        Log::warning('Failed login attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            'email' =>['بيانات الدخول غير صحيحة.'],
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index')
            ->with('success', 'تم تسجيل الخروج بنجاح.');
    }

}
