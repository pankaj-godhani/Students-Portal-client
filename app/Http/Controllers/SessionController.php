<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Student;
use App\Models\StudentSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'start_time' => 'required|date',
            'target' => 'required',
            'is_repeated' => 'boolean',
        ]);

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes(15);

//        // Check for overlapping sessions
//        $overlap = Session::where('student_id', $request->student_id)
//            ->where(function ($query) use ($startTime, $endTime) {
//                $query->whereBetween('start_time', [$startTime, $endTime])
//                    ->orWhereBetween('end_time', [$startTime, $endTime]);
//            })
//            ->exists();
//
//        if ($overlap) {
//            return response()->json(['error' => 'Session overlaps with an existing one'], 422);
//        }

        // Check if the session is within the student's available days (assuming availability is handled elsewhere)

        // Create the session
        $session = Session::create([
            'user_id' => auth()->id(),
//            'student_id' => $request->student_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'target' => $request->target,
            'is_repeated' => $request->is_repeated ?? false,
        ]);


        $studentSession = StudentSession::create([
            'student_id' => $request->student_id,
            'session_id' => $session->id,
        ]);
        // Schedule notification (assuming you have a notification system in place)
        // Notification logic would go here

        return response()->json($session, 201);
    }

    public function rate(Request $request, StudentSession $studentSession)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $studentSession->update([
            'rating' => $request->rating,
        ]);

        return response()->json($studentSession, 200);
    }
}

