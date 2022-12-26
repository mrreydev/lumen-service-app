<?php

namespace App\Http\Controllers;

use App\Models\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Store Profile
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validationRules = [
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'summary' => 'required|string|min:10',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'address' => 'required|string|min:10'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_OK);
        }

        $profile = Profile::where('user_id', Auth::user()->id)->first();

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = Auth::user()->id;
        }

        $profile->first_name = $input['first_name'];
        $profile->last_name = $input['last_name'];
        $profile->summary = $input['summary'];
        $profile->gender = $input['gender'];
        $profile->birth_date = $input['birth_date'];
        $profile->address = $input['address'];

        if ($request->hasFile('image')) {
            $firstName = str_replace(' ', '_', $input['first_name']);
            $lastName = str_replace(' ', '_', $input['last_name']);

            $imgName = Auth::user()->id.'_'.$firstName.'_'.$lastName;
            $request->file('image')->move(storage_path('uploads/image_profile'), $imgName);

            $current_image_path = storage_path('avatar').'/'.$profile->image;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $profile->image = $imgName;
        }

        if ($profile->save()) {
            $response = [
                'message' => 'Create Profile Success',
                'status_code' => Response::HTTP_CREATED,
                'data' => $profile
            ];

            return response()->json($response, Response::HTTP_CREATED);
        }

        $response = [
            'message' => 'Create Profile Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Show Profile
     */
    public function show(Request $request, $userId)
    {
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            $response = [
                'message' => 'Profile Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        $response = [
            'message' => 'Get Profile Success',
            'status_code' => Response::HTTP_OK,
            'data' => $profile
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Get Image Profile
     */
    public function image($imageName) {
        // dd($imageName);
        $imagePath = storage_path('uploads/image_profile').'/'.$imageName;
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, Response::HTTP_OK)->header('Content-Type', 'image/jpeg');
        }

        $response = [
            'message' => 'Image Not Found'
        ];

        return response()->json($response, Response::HTTP_NOT_FOUND);
    }
}
