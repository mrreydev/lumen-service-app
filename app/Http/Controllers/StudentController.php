<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
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
        $students = Student::OrderBy('id', 'ASC')->paginate();

        foreach ($students as $key => $student) {
            $student->major;
        }

        $response = [
            'messasge' => 'Get Students Success',
            'status_code' => Response::HTTP_OK,
            'data' => $students
        ];

        return response()->json($response, $response['status_code']);
    }

    public function create(Request $request)
    {
        $checkMajor = Major::find($request->major_id);

        if ($checkMajor) {
            $student = new Student();

            //! Create NIM -> app/helpers.php
            $nim = generateParentNum();

            $student->nim = $nim;
            $student->name = $request->name;
            $student->major_id = $request->major_id;

            if ($student->save()) {
                $student->major;
                $response = [
                    'message' => 'Create Student Success',
                    'status_code' => Response::HTTP_CREATED,
                    'data' => $student
                ];

                return response()->json($response, $response['status_code']);
            }
        }

        $response = [
            'message' => 'Create Student Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ];

        return response()->json($response, $response['status_code']);
    }


    public function show($id) {
        $student = Student::where('id', $id)
                            ->orWhere('nim', $id)->first();

        if ($student) {
            $student->major;
            $response = [
                'message' => 'Show Student Success',
                'status_code' => Response::HTTP_OK,
                'data' => $student
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Student Not Found',
            'status_code' => Response::HTTP_NOT_FOUND,
        ];

        return response()->json($response, $response['status_code']);
    }

    public function update(Request $request, $id)
    {
        $student = Student::where('id', $id)
                            ->orWhere('nim', $id)->first();
        $checkMajor = Major::find($request->major_id);

        if (!isset($student)) {
            $response = [
                'message' => 'Update Student Failed, Student Not Found',
                'status_code' => Response::HTTP_NOT_FOUND,
            ];

            return response()->json($response, $response['status_code']);
        }

        if (!isset($checkMajor)) {
            $response = [
                'message' => 'Update Student Failed, Major Not Valid',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];

            return response()->json($response, $response['status_code']);
        }

        $student->name = $request->name;
        $student->major_id = $request->major_id;

        if ($student->save()) {
            $student->major;
            $response = [
                'message' => 'Update Student Success',
                'status_code' => Response::HTTP_OK,
                'data' => $student
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Update Student Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ];

        return response()->json($response, $response['status_code']);
    }

    public function delete($id)
    {
        $student = Student::where('id', $id)
                            ->orWhere('nim', $id)->first();

        if (!isset($student)) {
            $response = [
                'message' => 'Delete Student Failed, Student Not Found',
                'status_code' => Response::HTTP_NOT_FOUND,
            ];

            return response()->json($response, $response['status_code']);
        }

        if ($student->delete()) {
            $response = [
                'message' => 'Delete Student Success',
                'status_code' => Response::HTTP_OK
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Delete Student Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }
}
