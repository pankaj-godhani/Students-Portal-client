<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use App\Models\Student;
use App\Models\StudentSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportTemplateController extends Controller
{

    public function getTemplates()
    {
        $templates = ReportTemplate::all();
        return response()->json($templates);
    }
    public function store(Request $request)
    {
        $request->validate([
            'template_content' => 'required|string',
        ]);

        ReportTemplate::create([
            'template_content' => $request->template_content,
        ]);

        return response()->json(['message' => 'Template saved successfully!'], 201);
    }

    public function show($id)
    {
        $template = ReportTemplate::find($id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        return response()->json($template);
    }

//    public function generateReport(Request $request)
//    {
//        $template = ReportTemplate::find($request->template_id);
//
//        if (!$template) {
//            return response()->json(['message' => 'Template not found'], 404);
//        }
//
//        $student = Student::find($request->student_id);
//        if (!$student) {
//            return response()->json(['message' => 'Student not found'], 404);
//        }
//
//        $sessions = StudentSession::where('student_id', $request->student_id)
//            ->with('session')
//            ->whereHas('session',function ($q) use ($request){
//                $q->orwhereBetween('start_time', [$request->start_date, $request->end_date]);
//                $q->orwhereBetween('end_time', [$request->start_date, $request->end_date]);
//            })
//            ->get();
//
//        $reportContent = '';
//        foreach ($sessions as $session) {
//            $content = $template->template_content;
//            $content = str_replace('@student_full_name', $student->first_name, $content);
//            $content = str_replace('@session_date', $session->created_at, $content);
//            $content = str_replace('@session_minutes', 15, $content);
//            $content = str_replace('@target_start_date ', $session->session->start_time, $content);
//            $content = str_replace('@target_end_date', $session->session->end_time, $content);
////            $content = str_replace('@target_start_date', $session->target_start_date, $content);
////            $content = str_replace('@target_end_date', $session->target_end_date, $content);
//            $content = str_replace('@target', $session->session->target, $content);
//            $content = str_replace('@session_rating', $session->rating, $content);
//            $reportContent .= $content . "\n\n";
//        }
//
//        $reportsDir = 'reports';
//        if (!Storage::exists($reportsDir)) {
//            Storage::makeDirectory($reportsDir);
//        }
//
//        $pdf = PDF::loadHTML($reportContent);
//        $pdfFileName = 'student_report_' . $student->id . '_' . time() . '.pdf';
//        $pdfPath = storage_path('app/reports/' . $pdfFileName);
//        $pdf->save($pdfPath);
//
//// Generate a URL that can be accessed via HTTP
//        return response()->download($pdfPath)->deleteFileAfterSend(true);
//    }

    public function generateReport(Request $request)
    {
        $template = ReportTemplate::find($request->template_id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $student = Student::find($request->student_id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $sessions = StudentSession::where('student_id', $request->student_id)
            ->with('session')
            ->whereHas('session',function ($q) use ($request){
                $q->orwhereBetween('start_time', [$request->start_date, $request->end_date])
                ->orwhereBetween('end_time', [$request->start_date, $request->end_date]);
            })
            ->get();

        if(!isset($sessions)){
            return response('Not session Add for this time period',400);
        }
//        dd($sessions);

        $reportsDir = 'reports/' . uniqid(); // Create a unique directory for each request
        if (!Storage::exists($reportsDir)) {
            Storage::makeDirectory($reportsDir);
        }

        $splitDuration = $request->splitMinutes; // Get the split duration in minutes from the request

        foreach ($sessions as $session) {
            $sessionStart = Carbon::parse($session->session->start_time);
            $sessionEnd = Carbon::parse($session->session->end_time);
            $sessionMinutes = $sessionStart->diffInMinutes($sessionEnd);
            for ($minute = 0; $minute < $sessionMinutes; $minute += $splitDuration) {
                $splitStart = $sessionStart->copy()->addMinutes($minute);
                $splitEnd = $splitStart->copy()->addMinutes($splitDuration);

                // Ensure the split end time does not exceed the session end time
                if ($splitEnd > $sessionEnd) {
                    $splitEnd = $sessionEnd;
                }

                $content = $template->template_content;
                $content = str_replace('@student_full_name', $student->first_name . ' ' . $student->last_name, $content);
                $content = str_replace('@session_date', $splitStart->format('Y-m-d'), $content);
                $content = str_replace('@session_minutes', $splitDuration, $content);
                $content = str_replace('@target_start_date', $splitStart->format('Y-m-d H:i'), $content);
                $content = str_replace('@target_end_date', $splitEnd->format('Y-m-d H:i'), $content);
                $content = str_replace('@target', $session->session->target, $content);
                $content = str_replace('@session_rating', $session->rating, $content);

                $reportContent = $content . "\n\n";

                $pdf = PDF::loadHTML($reportContent);
                $pdfFileName = 'student_report_' . $student->id . '_' . $splitStart->timestamp . '.pdf';
                $pdfPath = storage_path('app/' . $reportsDir . '/' . $pdfFileName);
                $pdf->save($pdfPath);
            }
        }

        // Create a ZIP file with all the generated PDFs
        $zipFileName = 'student_reports_' . $student->id . '_' . time() . '.zip';
        $zipFilePath = storage_path('app/reports/' . $zipFileName);

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
            $files = Storage::files($reportsDir);

            foreach ($files as $file) {
                $zip->addFile(storage_path('app/' . $file), basename($file));
            }

            $zip->close();
        }

        // Delete the temporary reports directory after creating the ZIP
        Storage::deleteDirectory($reportsDir);

        // Return the ZIP file for download and delete it after sending
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }



}
