<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('tasks');

        // Search
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Sorting
        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'asc');
        $query->orderBy($sort, $order);

       
        if ($request->query('withTrashed')) {
            $query->withTrashed();
        }

        $perPage = $request->query('per_page', 10);
        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->load('tasks');
        return new UserResource($user);
    }

    public function show($id)
    {
        $user = User::with('tasks')->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        $user->load('tasks');
        return new UserResource($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

         
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return response()->json(['message' => 'Invalid password'], 401);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $user->delete();
        return response()->json(['message' => 'User soft deleted'], 200);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Trashed user not found');
        }

        $user->restore();
        $user->load('tasks');
        return new UserResource($user);
    }

    public function trashed(Request $request)
    {
        $query = User::onlyTrashed()->with('tasks');
        $perPage = $request->query('per_page', 10);
        $users = $query->paginate($perPage);

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No trashed users found'], 200);
        }

        return UserResource::collection($users);
    }
}