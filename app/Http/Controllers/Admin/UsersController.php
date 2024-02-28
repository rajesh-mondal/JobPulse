<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller {
    public function index() {
        $users = User::orderBy( 'created_at', 'DESC' )->paginate( 10 );
        return view( 'admin.users.list', [
            'users' => $users,
        ] );
    }

    public function edit( $id ) {
        $user = User::findOrFail( $id );

        return view( 'admin.users.edit', [
            'user' => $user,
        ] );
    }

    public function update( $id, Request $request ) {

        $validator = Validator::make( $request->all(), [
            'name'  => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
        ] );

        if ( $validator->passes() ) {

            $user = User::find( $id );
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();

            session()->flash( 'success', 'User information updated successfully.' );

            return response()->json( [
                'status' => true,
                'errors' => [],
            ] );

        } else {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors(),
            ] );
        }
    }
}
