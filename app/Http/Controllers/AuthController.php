<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

/**
 * AuthController handles the HTTP requests related to user authentication,
 * including registration, login, logout, and retrieving the authenticated user's information.
 */
class AuthController extends Controller
{
    /**
     * @var AuthService $authService The service layer that handles the business logic for authentication.
     */
    protected $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService The service that handles authentication logic.
     *
     * This constructor initializes the AuthController with an instance of AuthService.
     * The AuthService is injected via dependency injection.
     */
    public function __construct(AuthService $authService)
    {
        // Assign the injected AuthService to the class property for use in other methods
        $this->authService = $authService;
    }

    /**
     * Handle the registration of a new user.
     *
     * @param RegisterRequest $request The validated request object containing registration data.
     * @return \Illuminate\Http\JsonResponse A JSON response with the registered user and JWT token.
     *
     * This method uses the AuthService to register a new user with the validated data from the request.
     * Upon successful registration, it returns a JSON response containing the user information and a JWT token.
     */
    public function register(RegisterRequest $request)
    {
        // Register the user using the AuthService and get the result (user and token)
        $result = $this->authService->register($request->validated());

        // Return a JSON response with the registered user and token, with a 201 status code
        return response()->json([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request The validated request object containing login credentials.
     * @return \Illuminate\Http\JsonResponse A JSON response with the authenticated user and JWT token.
     *
     * This method uses the AuthService to log in a user with the validated credentials from the request.
     * Upon successful login, it returns a JSON response containing the user information and a JWT token.
     */
    public function login(LoginRequest $request)
    {
        // Log in the user using the AuthService and get the result (user and token)
        $result = $this->authService->login($request->validated());

        // Return a JSON response with the authenticated user and token
        return response()->json($result);
    }

    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response confirming successful logout.
     *
     * This method uses the AuthService to log out the currently authenticated user.
     * Upon successful logout, it returns a JSON response confirming the operation.
     */
    public function logout()
    {
        // Log out the current user using the AuthService
        $this->authService->logout();

        // Return a JSON response with a success message
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Retrieve the currently authenticated user's information.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with the authenticated user's information.
     *
     * This method uses the AuthService to retrieve the currently authenticated user's information.
     * It returns a JSON response containing the user details.
     */
    public function me()
    {
        // Retrieve the authenticated user's information using the AuthService
        $user = $this->authService->me();

        // Return a JSON response with the user information
        return response()->json($user);
    }
}

