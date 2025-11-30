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
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration (Username Only - No Email)
     */
    public function register(Request $request)
    {
        // Rate limiting for registration attempts
        $key = 'register-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        // Validate input - NO EMAIL REQUIRED
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}a-zA-Z\s]+$/u'
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                'min:3',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/', // Only letters, numbers, underscores
                'not_regex:/[<>"\']/'
            ],
            'password' => [
                'required',
                'confirmed',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.regex' => 'الاسم يمكن أن يحتوي فقط على حروف ومسافات.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.min' => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل.',
            'username.max' => 'اسم المستخدم يجب ألا يتجاوز 50 حرف.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل. اختر اسماً آخر.',
            'username.regex' => 'اسم المستخدم يمكن أن يحتوي فقط على حروف إنجليزية وأرقام وشرطة سفلية (_).',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        ]);

        RateLimiter::hit($key, 300);

        try {
            // Create user WITHOUT email
            $user = User::create([
                'name' => strip_tags($validated['name']),
                'username' => strtolower(trim($validated['username'])),
                'email' => null, // No email required
                'password' => Hash::make($validated['password']),
            ]);

            // Log the user in
            Auth::login($user);

            // Regenerate session
            $request->session()->regenerate();

            // Clear rate limiter
            RateLimiter::clear($key);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'username' => $user->username
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'تم إنشاء الحساب بنجاح! مرحباً بك ' . $user->name);

        } catch (\Exception $e) {
            Log::error('Registration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login (Username Only)
     */
    public function login(Request $request)
    {
        // Rate limiting
        $key = 'login-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        // Validate credentials
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'اسم المستخدم مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        // Sanitize username
        $credentials['username'] = strtolower(trim($credentials['username']));

        // Attempt authentication
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            Log::info('User logged in', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'مرحباً بعودتك ' . Auth::user()->name . '!');
        }

        // Failed attempt
        RateLimiter::hit($key, 60);

        Log::warning('Failed login attempt', [
            'username' => $credentials['username'],
            'ip' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            'username' => ['اسم المستخدم أو كلمة المرور غير صحيحة.'],
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $username = Auth::user()->username ?? 'Unknown';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['username' => $username]);

        return redirect()->route('index')
            ->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google Redirect Error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['error' => 'حدث خطأ في الاتصال بـ Google. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            Log::info('Google User Data Received', [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'id' => $googleUser->getId(),
            ]);

            $email = strtolower(trim($googleUser->getEmail()));

            if (empty($email)) {
                throw new \Exception('لم يتم الحصول على البريد الإلكتروني من Google');
            }

            // Check if user exists by email OR google_id
            $user = User::where('email', $email)
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id]);

                // Update Google data if not set
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                    $user->email = $email;
                    $user->avatar = $googleUser->getAvatar();
                    $user->email_verified_at = now();
                    $user->save();
                }
            } else {
                Log::info('Creating new user from Google');

                // Generate unique username from email
                $baseUsername = $this->generateUsernameFromEmail($email);
                $username = $this->ensureUniqueUsername($baseUsername);

                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null,
                ]);

                Log::info('New user created from Google', [
                    'user_id' => $user->id,
                    'username' => $username
                ]);
            }

            // Log in
            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'مرحباً بك ' . $user->name . '! تم تسجيل الدخول بنجاح');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('Invalid State Exception', [
                'message' => $e->getMessage(),
            ]);

            $request->session()->forget('state');

            return redirect()->route('login')
                ->withErrors(['error' => 'انتهت صلاحية الجلسة. يرجى المحاولة مرة أخرى.']);

        } catch (\Exception $e) {
            Log::error('Google OAuth Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('login')
                ->withErrors(['error' => 'حدث خطأ أثناء تسجيل الدخول عبر Google.']);
        }
    }

    /**
     * Generate username from email
     */
    private function generateUsernameFromEmail(string $email): string
    {
        $username = explode('@', $email)[0];
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        $username = strtolower($username);

        if (strlen($username) < 3) {
            $username .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 5);
        }

        return $username;
    }

    /**
     * Ensure username is unique
     */
    private function ensureUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
