<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class CredentialsController extends Controller
{
    protected $client;
    private $user;
    public function __construct()
    {
        $this->client = Client::find(2);
        $this->user = Auth::user();
    }

    /**
     * Create passport auto login and return token
     *
     * @param Request $request
     * @param string $grant_type
     * @return void
     */
    public function issuePassport($request, $grant_type = 'password')
    {
        $params = [
            'grant_type' => $grant_type,
            'client_id' => optional($this->client)->id,
            'client_secret' => optional($this->client)->secret,
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '*',
        ];
        request()->request->add($params);
        $proxy = Request::create('/oauth/token', 'POST');

        return Route::dispatch($proxy);
    }
    /**
     * Checking if authorization, return expire date, then log out or get new token
     *
     * @return Response
     */
    public function authority()
    {
        $url = '/api/auth/profile';
        $request = Request::create($url);

        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Authorization', request()->header('Authorization'));

        $response = Route::dispatch($request);
        $token = DB::table('oauth_access_tokens')->where('user_id', auth()->user()->id)->latest()->first();

        $refreshToken = DB::table('oauth_refresh_tokens')->whereId($token->id)->first();

        if ($response->getStatusCode() != 401) {
            return Response::json(['success' => [
                'status' => 200,
                'token_expired_at' => $token->expires_at,
                'refresh_token_expire_at' => optional($refreshToken)->expires_at
            ]]);
        };
        return Response::json('Something went wrong');
    }
    /**
     * Login controller
     *
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        return $this->issuePassport($request);
    }
    /**
     * Register Controller
     *
     * @param RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        $access = array_merge($request->validated(), [
            'password' => Hash::make($request->password),
        ]);

        $user = User::create($access);
        $token = '';
        if ($user) {
            request()->request->add(['email' => $request->email, 'password' => $request->password]);
            $requestLogin = Request::create('/api/auth/login', 'post');
            $token = Route::dispatch($requestLogin);
        }
        // changing code verification to make sure user has receive the code
        return Response::json(["message" => "Register successfully", 'token' => json_decode($token->getContent()) ?? [] , 'data' => new UserResource($user)]);
    }

    /**
     * Refresh token, create another one
     *
     * @param Request $request
     */
    public function refresh(Request $request)
    {
        $request->validate(['refresh_token' => 'required']);
        return $this->issuePassport($request, 'refresh_token');
    }
    /**
     * Log out or revoke another token
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        foreach (Auth::user()->tokens as $t) {
            $t->revoke();
        }
        $id  = app(JwtParser::class)->parse($token)->claims()->get('jti');
        $repo = app(RefreshTokenRepository::class);
        $repo->revokeRefreshTokensByAccessTokenId($id);

        return Response::json(['message' => 'user successfully logout']);
    }

    /**
     * Get Profile of User
     *
     * @return Collections
     */
    public function profile()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Get Code to verify code
     *
     * @param Request $request
     * @return void
     */
    public function codeVerification(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|regex:/^[+-]?\d+$/|min:9|max:15',
                'code' => 'required|min:6|max:10'
            ]);
            $user = $this->findUserViaPhone($request->phone);
            if (!$user) :
                throw new ModelNotFoundException('incorrect phone number');
            endif;

            if ($user->code_verification_at != null || $user->domain !== 'phone') {
                return Response::sendError("Code has already been verified, or you would't allow to log on the phone");
            }
            $user->code_verification_at = Carbon::now();
            $user->save();
            return Response::sendSuccess("Code has been verified");

        } catch (ModelNotFoundException $e) {
            return Response::sendError($e->getMessage(), 404);

        } catch (ErrorException $e) {
            return Response::sendError("Something went wrong", 500);

        }
    }

    /**
     * Get User by phones
     *
     * @param string $phone
     * @return User
     */
    public function findUserViaPhone($phone)
    {
        return User::firstWhere('phone', $phone);
    }
}
