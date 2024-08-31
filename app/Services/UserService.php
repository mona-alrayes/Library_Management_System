<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class UserService
 * 
 * Handles operations related to users including CRUD operations, registration, and updates.
 */
class UserService
{
    /**
     * Retrieve all users with pagination.
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator|array
     * Returns a paginated list of users with their roles or an error response.
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
     * @param array $data
     * The array containing user registration data including 'name', 'email', 'password', and 'role'.
     * 
     * @return array
     * An array containing the user resource, a JWT token, or an error response.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registerUser(array $data): array
    {
        try {
            // Ensure the role exists before proceeding
            $role = Role::findByName($data['role']);

            if (!$role) {
                throw ValidationException::withMessages([
                    'role' => ['The selected role is invalid.'],
                ]);
            }

            // Create a new user with the provided data
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
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
     * @param array $data
     * The array containing updated user data. The 'password' field, if present, will be hashed.
     * @param int $id
     * The ID of the user to be updated.
     * 
     * @return User
     * The updated user instance.
     * 
     * @throws \Exception
     * Throws an exception if the user is not found or if an error occurs during the update.
     */
    public function updateUser(array $data, $id): User
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::findOrFail($id);

            // Update only fields that are present in the data array
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Update user with the filtered data
            $user->update(array_filter($data));

            // Return the updated user instance
            return $user;
        } catch (Exception $e) {
            // Handle any other exceptions
            throw new Exception('An error occurred during updating: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a user by ID.
     * 
     * @param int $id
     * The ID of the user to be retrieved.
     * 
     * @return User
     * The user instance if found.
     * 
     * @throws \Exception
     * Throws an exception if the user is not found or if an error occurs during retrieval.
     */
    public function getUserById($id): User
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::with('roles')->find($id);
            if (!$user) {
                throw new Exception('User not found!');
            }
            return $user;
        } catch (Exception $e) {
            // Handle any other exceptions
            throw new Exception('An error occurred while retrieving the user: ' . $e->getMessage());
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
     * @throws \Exception
     * Throws an exception if the user is not found or if an error occurs during deletion.
     */
    public function deleteUser($id): array
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::findOrFail($id);
            if (!$user) {
                throw new Exception('User not found!');
            }
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
