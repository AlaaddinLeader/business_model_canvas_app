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
use Illuminate\Support\Str;

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
     * Handle user registration with secure validation
     */
    public function register(Request $request)
    {
        // Rate limiting for registration attempts
        $key = 'register-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
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
        ], [
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

        try {
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
        } catch (\Exception $e) {
            return back()->withInput($request->except('password', 'password_confirmation'))
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
     * Handle user login with security measures
     */
    public function login(Request $request)
    {
        // Rate limiting for login attempts
        $key = 'login-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        // Validate credentials
        $credentials = $request->validate([
            'email' => ['required', 'email', 'not_regex:/[<>"\']/'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => ' البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        // Sanitize email
        $credentials['email'] = strtolower(trim($credentials['email']));

        // Attempt authentication
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
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
            'email' => ['بيانات الدخول غير صحيحة.'],
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
                'trace' => $e->getTraceAsString()
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
            // Get user info from Google (try with stateless first)
            $googleUser = Socialite::driver('google')->user();

            Log::info('Google User Data Received', [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'id' => $googleUser->getId(),
            ]);

            // Sanitize and normalize email
            $email = strtolower(trim($googleUser->getEmail()));

            // Validate email
            if (empty($email)) {
                throw new \Exception('لم يتم الحصول على البريد الإلكتروني من Google');
            }

            // Check if user exists with this email
            $user = User::where('email', $email)->first();

            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id]);

                // Update existing user with Google ID if not set
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                    $user->avatar = $googleUser->getAvatar();

                    // Only set email_verified_at if it's null
                    if (is_null($user->email_verified_at)) {
                        $user->email_verified_at = now();
                    }

                    $user->save();
                    Log::info('User updated with Google data', ['user_id' => $user->id]);
                }
            } else {
                Log::info('Creating new user from Google');

                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null,
                ]);

                Log::info('New user created successfully', ['user_id' => $user->id]);
            }

            // Log the user in with remember me
            Auth::login($user, true);

            Log::info('User authenticated successfully', ['user_id' => $user->id]);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'مرحباً بك! تم تسجيل الدخول بنجاح');
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('Invalid State Exception - Session mismatch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clear any existing session data and retry
            $request->session()->forget('state');

            return redirect()->route('login')
                ->withErrors(['error' => 'انتهت صلاحية الجلسة. يرجى المحاولة مرة أخرى.']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('Google API Client Error', [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response'
            ]);

            return redirect()->route('login')
                ->withErrors(['error' => 'خطأ في الاتصال بـ Google. يرجى التحقق من الإعدادات.']);
        } catch (\Exception $e) {
            Log::error('Google OAuth General Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->withErrors(['error' => 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.']);
        }
    }
}
