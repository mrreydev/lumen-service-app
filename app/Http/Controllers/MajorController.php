<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MajorController extends Controller
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

    public function index()
    {
        $majors = Major::OrderBy('id', 'ASC')->paginate();

        $response = [
            'message' => 'Get Majors Success',
            'data' => $majors,
            'status_code' => Response::HTTP_OK
        ];

        return response()->json($response, 200);
    }

    public function create(Request $request)
    {
        $major = new Major();

        $major->name = $request->name;

        $response = [
            'message' => 'Create Major Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        if ($major->save()) {
            $response['message'] = 'Create Major Success';
            $response['status_code'] = Response::HTTP_CREATED;
        }

        return response()->json($response, $response['status_code']);
    }

    public function update(Request $request, $id)
    {
        $name = $request->name;
        $major = Major::find($id);

        $major->name = $name;

        $response = [
            'message' => 'Update Major Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        if ($major->save()) {
            $response['message'] = 'Update Major Success';
            $response['status_code'] = Response::HTTP_OK;
        }

        return response()->json($response, $response['status_code']);
    }

    public function delete($id)
    {
        $major = Major::find($id);

        $response = [
            'message' => 'Delete Major Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        if (isset($major) && $major->delete()) {
            $response['message'] = 'Delete Major Success';
            $response['status_code'] = Response::HTTP_OK;
        }

        return response()->json($response, $response['status_code']);
    }
}
