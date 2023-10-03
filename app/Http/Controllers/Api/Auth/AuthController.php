<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Mail\SendVerificationEmail;
use App\Traits\ApiResponser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tzsk\Otp\Facades\Otp;

class AuthController extends Controller
{
    use ApiResponser;

    protected User $userModel;

    protected Role $roleModel;

    /**
     * Create a new AuthController instance.
     *
     * @param User $userModel
     * @return void
     */
    public function __construct(User $userModel, Role $roleModel)
    {
        $this->userModel = $userModel;
        $this->roleModel = $roleModel;
        $this->middleware('auth', ['except' => ['login', 'register', 'verifyEmail']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
           return $this->errorResponse('Invalid credential', 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Register a new user.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        $input = [
            'name' => $request->validated('name'),
            'username' => $request->validated('username'),
            'office' => $request->validated('office'),
            'email' => $request->validated('email'),
            'password' => bcrypt($request->validated('password')),
            'role_id' => $this->roleModel->firstWhere('alias', '=', 'programmer')->id
        ];

        $verificationCode = Otp::generate($input['email']);

        Mail::to($request->validated('email'))->send(new SendVerificationEmail($verificationCode));

        DB::transaction(function () use ($input) {
            return $this->userModel->create($input);
        });

        return $this->login($request);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user()->load('role'));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully log out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Verify email.
     *
     * @return  
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $this->userModel
            ->where('email', '=', $request->validated('email'))
            ->first();

        if ($user->email_verified_at) {
            return $this->errorResponse('Email already verified', 409);
        }

        $isVerificationCodeValid = Otp::match($request->validated('verification_code'), $request->validated('email'));

        if (!$isVerificationCodeValid) {
            return $this->errorResponse('Invalid or expired verification code', 410);
        }


        $user->email_verified_at = now();
        $user->save();

        return $this->successResponse([
            'message' => 'Email successfully verified',
            'data' => $user
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token)
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
