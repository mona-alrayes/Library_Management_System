<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * Class UserService
 * 
 * Handles operations related to users including CRUD operations and user registration.
 */
class UserService
{
    /**
     * Retrieve all users with pagination.
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator|array
     * Returns paginated list of users with their roles or an error response.
     */
    public function getAll()
    {
        try {
            // Retrieve users along with their roles, paginated by 5 per page
            $users = User::with('roles')->paginate(5);
            return $users;
        } catch (Exception $e) {
            // Handle any exceptions that may occur
            return [
                'status' => 'error',
                'message' => 'An error occurred while retrieving users.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register a new user.
     * 
     * @param Request $request
     * The request object containing user registration data.
     * 
     * @return array
     * An array containing the user resource and a newly generated JWT token, or an error response.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function RegisterUser(Request $request): array
    {
        try {
            // Ensure the role exists before proceeding
            $role = Role::findByName($request->role);

            if (!$role) {
                throw ValidationException::withMessages([
                    'role' => ['The selected role is invalid.'],
                ]);
            }

            // Create a new user with the provided data
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign the role to the user
            $user->assignRole($role);

            // Generate a JWT token for the user
            $token = auth()->login($user);

            // Prepare the response data
            return [
                'status' => 'success',
                'message' => 'User registered successfully.',
                'data' => new UserResource($user),
                'user-token' => $token,
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An error occurred during registration.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing user.
     * 
     * @param Request $request
     * The request object containing updated user data.
     * @param int $id
     * The ID of the user to be updated.
     * 
     * @return array
     * An array containing the updated user resource or an error response.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateUser(Request $request, $id): array
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::findOrFail($id);

            // Prepare data for update
            $data = $request->only('name', 'email', 'password');

            // Update only fields that are present in the request
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']); 
            }

            // Update user with the filtered data
            $user->update(array_filter($data));

            // Return the updated user resource
            return [
                'status' => 'success',
                'message' => 'User updated successfully.',
                'data' => new UserResource($user),
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An error occurred during updating.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve a user by ID.
     * 
     * @param int $id
     * The ID of the user to be retrieved.
     * 
     * @return array
     * An array containing the user resource or an error response.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getUserById($id): array
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::with('roles')->findOrFail($id);

            return [
                'status' => 'success',
                'message' => 'User retrieved successfully.',
                'data' => new UserResource($user),
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An error occurred while retrieving user.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a user by ID.
     * 
     * @param int $id
     * The ID of the user to be deleted.
     * 
     * @return array
     * An array containing a success message or an error response.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteUser($id): array
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::findOrFail($id);

            // Delete the user
            $user->delete();
            
            // Return a success message
            return [
                'status' => 'success',
                'message' => 'User deleted successfully.',
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An error occurred during deletion.',
                'errors' => $e->getMessage(),
            ];
        }
    }
}
