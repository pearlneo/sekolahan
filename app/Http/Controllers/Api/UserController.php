<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'links' => [
                    'self' => url('/api/users/' . $item->id)
                ]
            ];
        });
        return $this->success([
            'users' => $users,
            'links' => [
                'self' => url('/api/users')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' =>'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $saveUser = $user->save();
        if ($saveUser) {
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'links' => [
                    'self' => url('/api/users/' . $user->id),   
                    'collection' => url('/api/users')
                ]
            ];
            return $this->success($data,201,'User berhasil ditambahkan');
        } else {
            return $this->failedResponse('User gagal ditambahkan!',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'links' => [
                'self' => url('/api/users/' . $user->id),
                'collection' => url('/api/users')
            ]
        ];
        return $this->success($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        } 
        
        $saved = $user->save();
        if ($saved) {
            return $this->success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'links' => [
                    'self' => url('/api/users/' . $user->id),
                    'collection' => url('/api/users')   
                ]
            ], 200, 'User berhasil diupdate');
        } else {
            return $this->failedResponse('User gagal diupdate',500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $deleteData = $user->delete();

        if ($deleteData) {
            return $this->success([
                'links' => [
                    'collection' => url('/api/users')
                ]
            ], 200, 'User berhasil dihapus');
        } else {
            return $this->failedResponse('User gagal dihapus!',500);
        }
    }

}