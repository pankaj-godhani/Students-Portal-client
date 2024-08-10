<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSession;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return Student::all();
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'age' => 'required',
            'gender' => 'required',
        ]);
        $availability = $request->input('availability', []);
        $validatedData = array_merge($validatedData, [
            'monday' => $availability['monday'] ?? false,
            'tuesday' => $availability['tuesday'] ?? false,
            'wednesday' => $availability['wednesday'] ?? false,
            'thursday' => $availability['thursday'] ?? false,
            'friday' => $availability['friday'] ?? false,
            'saturday' => $availability['saturday'] ?? false,
            'sunday' => $availability['sunday'] ?? false,
        ]);

        return Student::create($validatedData);
    }

    public function sessions($studentId){
        $data['session'] = StudentSession::where('student_id',$studentId)->with(['student','session'])->get();
        $data['student'] = Student::find($studentId);
        return $data;
    }

    public function rate(Request $request, Session $session)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $session->update([
            'rating' => $request->rating,
        ]);

        return response()->json($session, 200);
    }
}
