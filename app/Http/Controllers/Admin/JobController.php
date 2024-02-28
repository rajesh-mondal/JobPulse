<?php

namespace App\Http\Controllers\Admin;

use App\Models\Job;
use App\Http\Controllers\Controller;

class JobController extends Controller {
    public function index() {
        $jobs = Job::orderBy( 'created_at', 'DESC' )->with( 'user', 'applications' )->paginate( 10 );
        return view( 'admin.jobs.list', [
            'jobs' => $jobs,
        ] );
    }
}
