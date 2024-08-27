<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Support\Exceptions\OAuthException;
use App\Support\Traits\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use Authenticatable;

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = Auth::attempt(credentials: $request->credentials())) {
            throw new OAuthException(code: 'invalid_credentials_provided');
        }

        return $this->responseWithToken(access_token: $token);
    }

    public function register(Request $request):JsonResponse
    {
        try {
            $rules = [
                "name"=>"required",
                "email" => "required|max:30|unique:users",
                "password" => "required|min:6"
            ];

            $validate = validate_requests($request, $rules);

            if (!$validate['passed']) {
                return response_with_status(0, $validate['message'], []);
            }

            $user = User::firstOrCreate(
                [
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password'])
                ]);

            return response()->json(["account"=>$user]);

        } catch (\Exception $e) {
            return response()->json(["error"=>$e->getMessage()]);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \App\Modules\Auth\Collections\TokenResource
     */
    public function refresh(): JsonResponse
    {
        return $this->responseWithToken(access_token: auth()->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return new JsonResponse(['sucess' => true]);
    }
}
