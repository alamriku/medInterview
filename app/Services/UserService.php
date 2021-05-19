<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUser($user)
    {
        return User::find($user);
    }

    public function updateUser($request, $id)
    {
        $profile = $this->getUser($id);
        $profile->name = $request['name'];
        $profile->email = $request['email'];
        if (isset($request['password'])) {
            $profile->password = Hash::make($request['password']);
        }
        $profile->update();
    }

    public function storeUser($request)
    {
        $user = new User();
        $user->name = $request["name"];
        $user->phone = $request["phone"];
        $user->email = $request["email"];
        $user->role = User::ROLE_LIBRARIAN;
        $user->password = Hash::make($request["password"]);
        $user->save();
    }

    public function destroy($user)
    {
        $user->delete();
    }
}