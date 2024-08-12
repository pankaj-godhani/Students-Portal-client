<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\StudentSession;
use App\Models\StudentTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Student;

class FileUploadController extends Controller
{

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:docx|max:2048',
        ]);

        $file = $request->file('file');



        $phpWord = IOFactory::load($file->getPathName());
        foreach ($phpWord->getSections() as $section) {
            // If a header exists, remove it
            if ($section->hasHeader()) {
                $section->removeHeader();
            }
        }
        $text = '';

        // Iterate over the sections and extract the text
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $elementType = get_class($element);

                switch ($elementType) {
                    case 'PhpOffice\PhpWord\Element\TextRun':
                        foreach ($element->getElements() as $textElement) {
                            if (method_exists($textElement, 'getText')) {
                                $text .= $textElement->getText() . "\n";
                            }
                        }
                        break;

                    case 'PhpOffice\PhpWord\Element\Table':
                        foreach ($element->getRows() as $row) {
                            foreach ($row->getCells() as $cell) {
                                foreach ($cell->getElements() as $cellElement) {
                                    if (method_exists($cellElement, 'getText')) {
                                        $text .= $cellElement->getText() . "\n";
                                    }
                                }
                            }
                        }
                        break;

                    // Add more cases if needed for other element types

//                    default:
//                        // Handle or skip unknown types
//                        break;
                }
            }
        }


        $this->storeParsedData($text,$request->student_id);


        // Parse the docx file

        return response()->json(['message' => 'File uploaded and parsed successfully.']);
    }

    protected function storeParsedData($text,$studentId)
    {
        $lines = explode("\n", $text);
        $studentData = [];
        $currentData = [];
        $tableType = null; // Variable to store the type of table being processed
        $numberOfKeysBetween=0;

//        dd($lines);
        foreach ($lines as $key=>$line) {
            $line = trim($line);

            if (strpos($line, 'Subject name') !== false) {
                $tableType = 'type1';
                if (!empty($currentData)) {
                    $studentData[] = $currentData;
                }
                $currentData = [];
            } elseif (strpos($line, 'Subject') !== false && strpos($line, 'name') === false) {
                $tableType = 'type2'; // Type 2: Theoretical Knowledge

                $subjectIndex = array_search("Subject", $lines);
                $targetIndex = array_search("Target", $lines);
                $numberOfKeysBetween = abs($targetIndex - $subjectIndex) + 1;
                if (!empty($currentData)) {
                    $studentData[] = $currentData;
                }
                $currentData = [];
            }

            if ($tableType === 'type1') {
                if (strpos($line, 'Subject name') !== false) {
                    $currentData['subject'] = $lines[$key + 2];
                }

                if (strpos($line, 'Area of weakness') !== false) {
                    if (!isset($currentData['area_of_weakness'])) {
                        $currentData['area_of_weakness'] = $lines[$key + 2];
                    }
                }

                if (strpos($line, 'Session start date') !== false || strpos($line, 'Start Date') !== false) {
                    $currentData['session_start_date'] = $lines[$key + 1];
                }

                if (strpos($line, 'Session end date') !== false || strpos($line, 'End Date') !== false) {
                    $currentData['session_end_date'] = $lines[$key + 1];
                }

                if (strpos($line, 'Improvement target') !== false || strpos($line, 'target') !== false) {
                    $currentData['improvement_target'] = $lines[$key + 1];
                }
            }
            elseif ($tableType === 'type2'){

                if ($line === 'Subject') {
                    $currentData['subject'] = $lines[$key + $numberOfKeysBetween];
                }
                if ($line === 'Area of weakness') {
                    $currentData['area_of_weakness'] = $lines[$key + $numberOfKeysBetween];
                }
                if ($line === 'Date') {
                    $dateRange = explode(' to ', $lines[$key + $numberOfKeysBetween]);
                    $currentData['session_start_date'] = $dateRange[0];
                    $currentData['session_end_date'] = isset($dateRange[1]) ? $dateRange[1] : null;
                }
                if($line === 'Start From'){
                    $currentData['session_start_date'] = $lines[$key + $numberOfKeysBetween];
                }
                if($line === 'End To'){
                    $currentData['session_end_date'] = $lines[$key + $numberOfKeysBetween];
                }
                if ($line === 'Target') {
                    $currentData['improvement_target'] = str_replace(" per session", '', $lines[$key + $numberOfKeysBetween]);
                    if (!empty($currentData)) {
                        $studentData[] = $currentData;
                    }
                    $currentData = [];
                    $lastKey = $key+$numberOfKeysBetween+1;
                    $lastKeyValue  = $lines[$key + $numberOfKeysBetween +1];
                    if( $lastKeyValue !== ""  || $lastKeyValue  !== 'Assessment'){
                        $currentData['subject'] = $lastKeyValue;
                        $currentData['area_of_weakness'] = $lines[$lastKey+1];

                        if($numberOfKeysBetween == 5){
                            $currentData['session_start_date'] = $lines[$key + $numberOfKeysBetween+2];
                            $currentData['session_end_date'] = $lines[$key + $numberOfKeysBetween+3];
                            $currentData['improvement_target'] =str_replace(" per session", '', $lines[$lastKey+4]);

                        }else{
                            $dateRange = explode(' to ', $lines[$lastKey+2]);
                            $currentData['session_start_date'] = $dateRange[0];
                            $currentData['session_end_date'] = isset($dateRange[1]) ? $dateRange[1] : null;
                            $currentData['improvement_target'] =str_replace(" per session", '', $lines[$lastKey+3]);
                        }
                    }
                }
            }
        }

        if (!empty($currentData)) {
            $studentData[] = $currentData;
        }


        foreach ($studentData as $student) {
            StudentTarget::create([
                'student_id' => $studentId,
                'subject' => $student['subject'],
                'area_of_weakness' => $student['area_of_weakness'],
                'session_start_date' => date('Y-m-d',strtotime($student['session_start_date'])),
                'session_end_date' => date('Y-m-d',strtotime($student['session_end_date'])),
                'improvement_target' => $student['improvement_target'],
            ]);

            $startTime = Carbon::parse(strtotime($student['session_start_date']));
            $endTime = $startTime->copy()->addMinutes(15);

            $session = Session::create([
                'user_id' => auth()->id(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'target' => $student['improvement_target'],
                'is_repeated' =>  false,
            ]);

            $studentSession = StudentSession::create([
                'student_id' => $studentId,
                'session_id' => $session->id,
            ]);

        }
    }



}

