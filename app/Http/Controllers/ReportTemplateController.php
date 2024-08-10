<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use App\Models\Student;
use App\Models\StudentSession;
use Barryvdh\DomPDF\Facade\Pdf;
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
                $q->orwhereBetween('start_time', [$request->start_date, $request->end_date]);
                $q->orwhereBetween('end_time', [$request->start_date, $request->end_date]);
            })
            ->get();
//        dd($sessions);

        $reportContent = '';
        foreach ($sessions as $session) {
            $content = $template->template_content;
            $content = str_replace('@student_full_name', $student->first_name, $content);
            $content = str_replace('@session_date', $session->created_at, $content);
            $content = str_replace('@session_minutes', 15, $content);
            $content = str_replace('@target_start_date ', $session->session->start_time, $content);
            $content = str_replace('@target_end_date', $session->session->end_time, $content);
//            $content = str_replace('@target_start_date', $session->target_start_date, $content);
//            $content = str_replace('@target_end_date', $session->target_end_date, $content);
            $content = str_replace('@target', $session->session->target, $content);
            $content = str_replace('@session_rating', $session->rating, $content);
            $reportContent .= $content . "\n\n";
        }

        $reportsDir = 'reports';
        if (!Storage::exists($reportsDir)) {
            Storage::makeDirectory($reportsDir);
        }

        $pdf = PDF::loadHTML($reportContent);
        $pdfFileName = 'student_report_' . $student->id . '_' . time() . '.pdf';
//        $pdfPath = $reportsDir . '/' . $pdfFileName;
        $pdfPath = storage_path('app/reports/' . $pdfFileName);
//        $pdf->save(storage_path('app/' . $pdfPath));
        $pdf->save($pdfPath);

// Generate a URL that can be accessed via HTTP
//        $pdfUrl = Storage::url($pdfPath);
//        $path = storage_path() . '/app/' . $pdfPath;
//        dd($path);
//        return response()->download('file:///C:/xampp8.2/htdocs/studentPortal/storage/app/reports/student_report_1_1723293113.pdf');
//        return response()->json(['pdf_url' => 'file:///C:/xampp8.2/htdocs/studentPortal/storage/app/reports/student_report_1_1723293113.pdf']);
        return response()->download($pdfPath)->deleteFileAfterSend(true);
//        return $pdf->download($pdfPath);
    }


}
