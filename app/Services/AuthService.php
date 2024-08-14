<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthService handles the authentication-related business logic, including user registration,
 * login, logout, and retrieving the currently authenticated user.
 */
class AuthService
{
    /**
     * Register a new user and return the user and JWT token.
     *
     * @param array $data The data required for registering a new user, including name, email, and password.
     * @return array An array containing the newly registered user instance and a JWT token.
     *
     * This method creates a new user in the database with the provided name, email, and password.
     * The password is securely hashed before storing. After the user is created, they are automatically
     * logged in, and a JWT token is generated and returned along with the user instance.
     *
     * @throws \Illuminate\Validation\ValidationException If the user registration fails.
     */
    public function register(array $data): array
    {
        // Create a new user with the provided data, ensuring the password is hashed
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Log the user in and generate a JWT token
        $token = Auth::login($user);

        // Return the user instance and the generated token
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Log in a user and return the JWT token.
     *
     * @param array $credentials The login credentials, including email and password.
     * @return array An array containing the authenticated user instance and a JWT token.
     *
     * This method attempts to log in a user using the provided credentials. If the credentials are correct,
     * a JWT token is generated and returned along with the authenticated user instance. If the credentials
     * do not match, a ValidationException is thrown.
     *
     * @throws \Illuminate\Validation\ValidationException If the login credentials are incorrect.
     */
    public function login(array $credentials): array
    {
        // Attempt to log in with the provided credentials; throw an exception if authentication fails
        if (! $token = Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Return the authenticated user instance and the generated token
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Log out the current user.
     *
     * This method logs out the currently authenticated user by invalidating their JWT token.
     * It uses the 'api' guard to perform the logout operation.
     */
    public function logout(): void
    {
        // Log out the current user by invalidating their token
        Auth::guard('api')->logout();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \App\Models\User|null The authenticated user instance, or null if no user is authenticated.
     *
     * This method returns the currently authenticated user. If no user is authenticated, it returns null.
     */
    public function me(): ?User
    {
        // Return the currently authenticated user, or null if no user is authenticated
        return Auth::user();
    }
}

