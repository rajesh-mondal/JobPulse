<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
// use SebastianBergmann\CodeCoverage\Driver\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller {
    public function registration() {
        return view( 'front.account.registration' );
    }

    public function processRegistration( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'name'             => 'required',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ] );

        if ( $validator->passes() ) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make( $request->password );
            $user->name = $request->name;
            $user->save();

            session()->flash( 'success', 'You have registerd successfully.' );

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

    public function login() {
        return view( 'front.account.login' );
    }

    public function authenticate( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ] );

        if ( $validator->passes() ) {

            if ( Auth::attempt( ['email' => $request->email, 'password' => $request->password] ) ) {
                return redirect()->route( 'account.profile' );
            } else {
                return redirect()->route( 'account.login' )->with( 'error', 'Either Email/Password is incorrect' );
            }
        } else {
            return redirect()->route( 'account.login' )
                ->withErrors( $validator )
                ->withInput( $request->only( 'email' ) );
        }
    }

    public function profile() {
        $id = Auth::user()->id;
        $user = User::where( 'id', $id )->first();
        return view( 'front.account.profile', [
            'user' => $user,
        ] );
    }

    public function updateProfile( Request $request ) {

        $id = Auth::user()->id;

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

            session()->flash( 'success', 'Profile updated successfully.' );

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

    public function updateProfilePic( Request $request ) {

        $id = Auth::user()->id;

        $validator = Validator::make( $request->all(), [
            'image' => 'required|image',
        ] );

        if ( $validator->passes() ) {

            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id . '-' . time() . '.' . $ext;
            $image->move( public_path( '/profile_pic/' ), $imageName );

            $sourcePath = public_path( '/profile_pic/' . $imageName );
            $manager = new ImageManager( Driver::class );
            $image = $manager->read( $sourcePath );

            $image->cover( 150, 150 );
            $image->toPng()->save( public_path( '/profile_pic/thumb/' . $imageName ) );

            File::delete( public_path( '/profile_pic/thumb/' . Auth::user()->image ) );
            File::delete( public_path( '/profile_pic/' . Auth::user()->image ) );

            User::where( 'id', $id )->update( ['image' => $imageName] );

            session()->flash( 'success', 'Profile picture updated successfully.' );

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

    public function logout() {
        Auth::logout();
        return redirect()->route( 'account.login' );
    }

    public function createJob() {

        $categories = Category::orderBy( 'name', 'ASC' )->where( 'status', 1 )->get();
        $jobTypes = JobType::orderBy( 'name', 'ASC' )->where( 'status', 1 )->get();

        return view( 'front.account.job.create', [
            'categories' => $categories,
            'jobTypes'   => $jobTypes,
        ] );
    }

    public function saveJob( Request $request ) {

        $rules = [
            'title'        => 'required|min:5|max:200',
            'category'     => 'required',
            'jobType'      => 'required',
            'vacancy'      => 'required|integer',
            'location'     => 'required|max:50',
            'description'  => 'required',
            'company_name' => 'required|min:3|max:75',

        ];

        $validator = Validator::make( $request->all(), $rules );

        if ( $validator->passes() ) {

            $job = new Job();
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->website;
            $job->save();

            session()->flash( 'success', 'Job added successfully.' );

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

    public function myJobs() {
        $jobs = Job::where( 'user_id', Auth::user()->id )->with( 'jobType' )

            ->orderBy( 'created_at', 'DESC' )->paginate( 10 );
        return view( 'front.account.job.my-jobs', [
            'jobs' => $jobs,
        ] );
    }

    public function editJob( Request $request, $id ) {

        $categories = Category::orderBy( 'name', 'ASC' )->where( 'status', 1 )->get();
        $jobTypes = JobType::orderBy( 'name', 'ASC' )->where( 'status', 1 )->get();

        $job = Job::where( [
            'user_id' => Auth::user()->id,
            'id'      => $id,
        ] )->first();

        if ( $job == null ) {
            abort( 404 );
        }

        return view( 'front.account.job.edit', [
            'categories' => $categories,
            'jobTypes'   => $jobTypes,
            'job'        => $job,
        ] );
    }

    public function updateJob( Request $request, $id ) {

        $rules = [
            'title'        => 'required|min:5|max:200',
            'category'     => 'required',
            'jobType'      => 'required',
            'vacancy'      => 'required|integer',
            'location'     => 'required|max:50',
            'description'  => 'required',
            'company_name' => 'required|min:3|max:75',

        ];

        $validator = Validator::make( $request->all(), $rules );

        if ( $validator->passes() ) {

            $job = Job::find( $id );
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->website;
            $job->save();

            session()->flash( 'success', 'Job updated successfully.' );

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
