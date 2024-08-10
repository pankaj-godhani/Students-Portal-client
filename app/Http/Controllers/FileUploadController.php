<?php

namespace App\Http\Controllers;

use App\Models\StudentTarget;
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

        $phpWord = IOFactory::load($file->getPathName(), 'Word2007');
        $text = '';
//dd($phpWord);
        // Iterate over the sections and extract the text
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $elementType = get_class($element);

                switch ($elementType) {
                    case 'PhpOffice\PhpWord\Element\TextRun':
                        foreach ($element->getElements() as $textElement) {
                            if (method_exists($textElement, 'getText')) {
                                $text .= $textElement->getText();
                            }
                        }
                        break;

                    case 'PhpOffice\PhpWord\Element\Table':
                        foreach ($element->getRows() as $row) {
                            foreach ($row->getCells() as $cell) {
                                foreach ($cell->getElements() as $cellElement) {
                                    if (method_exists($cellElement, 'getText')) {
                                        $text .= $cellElement->getText() . " ";
                                    }
                                }
                            }
                        }
                        break;

                    // Add more cases if needed for other element types

                    default:
                        // Handle or skip unknown types
                        break;
                }
            }
        }

        $this->storeParsedData($text,$request->student_id);


        // Parse the docx file

        return response()->json(['message' => 'File uploaded and parsed successfully.']);
    }


    private function storeParsedData($text, $studentId)
    {
        // Split the text into sections based on a unique identifier (e.g., 'Bx')
        $sections = preg_split('/Bx \d+\./', $text);
        foreach ($sections as $section) {

            // Trim the section to remove any unnecessary whitespace
            $section = trim($section);
            if (empty($section)) {
                continue;
            }
//            dd($section);

            // Extract data using regex or other string operations
            preg_match('/Subject name\s+([^\n]+)/', $section, $subjectMatch);
            preg_match('/Area of weakness\s+([^\n]+)/', $section, $areaMatch);
            preg_match('/Session start date\s+([^\n]+)/', $section, $startDateMatch);
            preg_match('/Session end date\s+([^\n]+)/', $section, $endDateMatch);
            preg_match('/target|Improvement target\s+(\d+)/i', $section, $targetMatch);


            // If all required data is found, store it in the database
            if (isset($subjectMatch[1], $areaMatch[1], $startDateMatch[1], $endDateMatch[1], $targetMatch[1])) {
                print_r($subjectMatch);
                StudentTarget::create([
                    'student_id' => $studentId, // Use the provided student ID
                    'subject' => trim($subjectMatch[1]),
                    'area_of_weakness' => trim($areaMatch[1]),
                    'session_start_date' => date('Y-m-d', strtotime(trim($startDateMatch[1]))),
                    'session_end_date' => date('Y-m-d', strtotime(trim($endDateMatch[1]))),
                    'improvement_target' => (int)trim($targetMatch[1]),
                ]);
            }
        }
    }



}

