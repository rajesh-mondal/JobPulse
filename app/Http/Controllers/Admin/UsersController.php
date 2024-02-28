<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;

class UsersController extends Controller {
    public function index() {
        $users = User::orderBy( 'created_at', 'DESC' )->paginate( 10 );
        return view( 'admin.users.list', [
            'users' => $users,
        ] );
    }
}
