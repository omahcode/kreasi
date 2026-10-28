<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentImportController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::all();

        return view('admin.students.import', compact('classes'));
    }

    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));

        $classes = SchoolClass::orderBy('name')->get();
        $sampleClassNames = $classes->pluck('name')->toArray();
        if (empty($sampleClassNames)) {
            $sampleClassNames = ['XII RPL 1', 'XII RPL 2', 'XI RPL 1', 'X RPL 1'];
        }

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="template_import_siswa_kreasi_rpl.csv"',
            ];

            $callback = function () use ($sampleClassNames) {
                $handle = fopen('php://output', 'w');
                // Write UTF-8 BOM so Excel opens it with proper UTF-8 encoding
                fwrite($handle, "\xEF\xBB\xBF");
                // Write CSV headers
                fputcsv($handle, ['Nama', 'NIS', 'Email', 'Kelas', 'Tanggal Lahir']);
                // Sample rows with birth date examples (DD-MM-YYYY or YYYY-MM-DD or DDMMYYYY)
                fputcsv($handle, ['Bima Satria Ramadhan', '2026120110', 'bima.satria@kreasi.test', $sampleClassNames[0], '15-08-2008']);
                fputcsv($handle, ['Dewi Sartika Putri', '2026120111', 'dewi.sartika@kreasi.test', $sampleClassNames[0], '22-11-2008']);
                fputcsv($handle, ['Fajar Nugraha', '2026120112', 'fajar.nugraha@kreasi.test', $sampleClassNames[1] ?? $sampleClassNames[0], '03-05-2009']);
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Generate formatted Excel (.xlsx) with clean columns, styling, and borders
        $spreadsheet = new Spreadsheet;

        // -------------------------------------------------------------
        // Sheet 1: Template Import Siswa
        // -------------------------------------------------------------
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Template Import Siswa');

        // Headers
        $headers = ['Nama', 'NIS', 'Email', 'Kelas', 'Tanggal Lahir'];
        foreach ($headers as $colIdx => $header) {
            $colLetter = chr(65 + $colIdx);
            $sheet1->setCellValue($colLetter.'1', $header);
        }

        // Header Styling
        $sheet1->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'name' => 'Segoe UI',
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E40AF'], // Professional Navy/Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF1E3A8A'],
                ],
            ],
        ]);
        $sheet1->getRowDimension(1)->setRowHeight(30);

        // Sample Data Rows
        $sampleData = [
            ['Bima Satria Ramadhan', '2026120110', 'bima.satria@kreasi.test', $sampleClassNames[0], '15-08-2008'],
            ['Dewi Sartika Putri', '2026120111', 'dewi.sartika@kreasi.test', $sampleClassNames[0], '22-11-2008'],
            ['Fajar Nugraha', '2026120112', 'fajar.nugraha@kreasi.test', $sampleClassNames[1] ?? $sampleClassNames[0], '03-05-2009'],
            ['Siti Nurhaliza', '2026120113', 'siti.nurhaliza@kreasi.test', $sampleClassNames[2] ?? $sampleClassNames[0], '19-12-2008'],
        ];

        // Format rows up to row 25 with borders and clean styling so users have neat columns ready to type in
        $maxPreformattedRow = 25;
        for ($r = 2; $r <= $maxPreformattedRow; $r++) {
            $dataIndex = $r - 2;
            if (isset($sampleData[$dataIndex])) {
                $sheet1->setCellValue('A'.$r, $sampleData[$dataIndex][0]);
                $sheet1->setCellValueExplicit('B'.$r, $sampleData[$dataIndex][1], DataType::TYPE_STRING);
                $sheet1->setCellValue('C'.$r, $sampleData[$dataIndex][2]);
                $sheet1->setCellValue('D'.$r, $sampleData[$dataIndex][3]);
                $sheet1->setCellValueExplicit('E'.$r, $sampleData[$dataIndex][4], DataType::TYPE_STRING);
            } else {
                $sheet1->getCell('B'.$r)->setValueExplicit('', DataType::TYPE_STRING);
                $sheet1->getCell('E'.$r)->setValueExplicit('', DataType::TYPE_STRING);
            }

            $isEven = ($r % 2 === 0);
            $rowStyle = [
                'font' => [
                    'name' => 'Segoe UI',
                    'size' => 10,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];

            if (! $isEven) {
                $rowStyle['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF8FAFC'],
                ];
            }

            $sheet1->getStyle("A{$r}:E{$r}")->applyFromArray($rowStyle);
            $sheet1->getRowDimension($r)->setRowHeight(24);
        }

        // Alignments
        $sheet1->getStyle("A2:A{$maxPreformattedRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet1->getStyle("B2:B{$maxPreformattedRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet1->getStyle("C2:C{$maxPreformattedRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet1->getStyle("D2:D{$maxPreformattedRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet1->getStyle("E2:E{$maxPreformattedRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column widths
        $sheet1->getColumnDimension('A')->setWidth(32);
        $sheet1->getColumnDimension('B')->setWidth(20);
        $sheet1->getColumnDimension('C')->setWidth(34);
        $sheet1->getColumnDimension('D')->setWidth(22);
        $sheet1->getColumnDimension('E')->setWidth(20);

        // Data validation dropdown for Kelas column (D2:D50)
        if (! empty($sampleClassNames)) {
            $classFormula = '"'.implode(',', $sampleClassNames).'"';
            for ($i = 2; $i <= 50; $i++) {
                $validation = $sheet1->getCell('D'.$i)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setPromptTitle('Pilih Kelas');
                $validation->setPrompt('Pilih salah satu nama kelas terdaftar');
                $validation->setFormula1($classFormula);
            }
        }

        // Sheet 1 Display settings
        $sheet1->setShowGridlines(true);
        $sheet1->freezePane('A2');

        // -------------------------------------------------------------
        // Sheet 2: Petunjuk & Daftar Kelas
        // -------------------------------------------------------------
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Petunjuk & Daftar Kelas');

        $sheet2->setCellValue('A1', 'PANDUAN PENGISIAN TEMPLATE IMPORT SISWA KREASI RPL');
        $sheet2->getStyle('A1')->getFont()->setName('Segoe UI')->setBold(true)->setSize(14)->getColor()->setARGB('FF1E3A8A');

        $sheet2->setCellValue('A3', '1. Aturan Pengisian Kolom:');
        $sheet2->getStyle('A3')->getFont()->setName('Segoe UI')->setBold(true)->setSize(11);

        $rules = [
            ['Kolom', 'Keterangan', 'Contoh'],
            ['Nama', 'Wajib diisi dengan nama lengkap siswa.', 'Bima Satria Ramadhan'],
            ['NIS', 'Wajib diisi dengan Nomor Induk Siswa yang unik (tidak boleh duplikat).', '2026120110'],
            ['Email', 'Wajib diisi dengan format email valid & belum terdaftar di sistem.', 'bima.satria@kreasi.test'],
            ['Kelas', 'Wajib diisi persis sama dengan nama kelas yang ada di tabel samping / dropdown.', 'XII RPL 1'],
            ['Tanggal Lahir', 'Format: DD-MM-YYYY, YYYY-MM-DD, atau DDMMYYYY. Digunakan otomatis sebagai password akun siswa (DDMMYYYY). Jika kosong, fallback ke NIS.', '15-08-2008 (Password: 15082008)'],
        ];

        $rIdx = 4;
        foreach ($rules as $i => $rule) {
            $sheet2->setCellValue('A'.$rIdx, $rule[0]);
            $sheet2->setCellValue('B'.$rIdx, $rule[1]);
            $sheet2->setCellValue('C'.$rIdx, $rule[2]);

            if ($i === 0) {
                $sheet2->getStyle("A{$rIdx}:C{$rIdx}")->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF334155']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF475569']]],
                ]);
                $sheet2->getRowDimension($rIdx)->setRowHeight(24);
            } else {
                $sheet2->getStyle("A{$rIdx}:C{$rIdx}")->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet2->getRowDimension($rIdx)->setRowHeight(22);
            }
            $rIdx++;
        }

        // Catatan Penting
        $rIdx += 1;
        $sheet2->setCellValue('A'.$rIdx, '* Catatan Penting:');
        $sheet2->getStyle('A'.$rIdx)->getFont()->setName('Segoe UI')->setBold(true)->getColor()->setARGB('FFDC2626');
        $rIdx++;
        $sheet2->setCellValue('A'.$rIdx, '• Password awal siswa otomatis dibuat dari Tanggal Lahir berformat DDMMYYYY (contoh: 15 Agustus 2008 -> 15082008). Jika tanggal lahir tidak diisi, password menggunakan NIS.');
        $sheet2->getStyle('A'.$rIdx)->getFont()->setName('Segoe UI')->setSize(10);
        $rIdx++;
        $sheet2->setCellValue('A'.$rIdx, '• Nama kelas harus sesuai dengan daftar kelas terdaftar (dapat dipilih lewat dropdown di Sheet 1).');
        $sheet2->getStyle('A'.$rIdx)->getFont()->setName('Segoe UI')->setSize(10);
        $rIdx++;
        $sheet2->setCellValue('A'.$rIdx, '• Anda dapat langsung mengunggah file hasil edit dalam format .xlsx atau .csv ke menu import.');
        $sheet2->getStyle('A'.$rIdx)->getFont()->setName('Segoe UI')->setSize(10);

        // Tabel Daftar Kelas
        $sheet2->setCellValue('E3', '2. Daftar Kelas Terdaftar di Sistem:');
        $sheet2->getStyle('E3')->getFont()->setName('Segoe UI')->setBold(true)->setSize(11);

        $sheet2->setCellValue('E4', 'No');
        $sheet2->setCellValue('F4', 'Nama Kelas');
        $sheet2->getStyle('E4:F4')->applyFromArray([
            'font' => ['name' => 'Segoe UI', 'bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF047857']]],
        ]);
        $sheet2->getRowDimension(4)->setRowHeight(24);

        $cIdx = 5;
        foreach ($classes as $no => $cls) {
            $sheet2->setCellValue('E'.$cIdx, $no + 1);
            $sheet2->setCellValue('F'.$cIdx, $cls->name);
            $sheet2->getStyle("E{$cIdx}:F{$cIdx}")->applyFromArray([
                'font' => ['name' => 'Segoe UI', 'size' => 10],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet2->getStyle('E'.$cIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getRowDimension($cIdx)->setRowHeight(22);
            $cIdx++;
        }

        // Sheet 2 widths & display
        $sheet2->getColumnDimension('A')->setWidth(16);
        $sheet2->getColumnDimension('B')->setWidth(50);
        $sheet2->getColumnDimension('C')->setWidth(30);
        $sheet2->getColumnDimension('D')->setWidth(6);
        $sheet2->getColumnDimension('E')->setWidth(8);
        $sheet2->getColumnDimension('F')->setWidth(26);
        $sheet2->setShowGridlines(true);

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_siswa_kreasi_rpl.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ], [
            'file.required' => 'Silakan pilih file Excel atau CSV yang akan diunggah.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
        ]);

        $path = $request->file('file')->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca isi file: '.$e->getMessage());
        }

        if (empty($rows) || count($rows) < 2) {
            return back()->with('error', 'File Excel kosong atau tidak memiliki baris data.');
        }

        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $colKey => $colName) {
            $normalized = strtolower(trim((string) $colName));
            if (str_contains($normalized, 'nama')) {
                $headerMap['nama'] = $colKey;
            } elseif (str_contains($normalized, 'nis')) {
                $headerMap['nis'] = $colKey;
            } elseif (str_contains($normalized, 'email')) {
                $headerMap['email'] = $colKey;
            } elseif (str_contains($normalized, 'kelas')) {
                $headerMap['kelas'] = $colKey;
            } elseif (str_contains($normalized, 'lahir') || str_contains($normalized, 'birth') || str_contains($normalized, 'tgl')) {
                $headerMap['birth_date'] = $colKey;
            }
        }

        if (! isset($headerMap['nama']) || ! isset($headerMap['nis']) || ! isset($headerMap['email']) || ! isset($headerMap['kelas'])) {
            return back()->with('error', 'Kolom tidak lengkap. Pastikan file memiliki kolom: Nama, NIS, Email, dan Kelas.');
        }

        $parsedRows = [];
        $existingEmails = User::pluck('email')->toArray();
        $existingNis = Student::pluck('nis')->toArray();
        $classes = SchoolClass::all()->keyBy(fn ($c) => strtolower(trim($c->name)));

        $validCount = 0;
        $errorCount = 0;

        foreach ($rows as $index => $row) {
            $nama = trim((string) ($row[$headerMap['nama']] ?? ''));
            $nis = trim((string) ($row[$headerMap['nis']] ?? ''));
            $email = trim((string) ($row[$headerMap['email']] ?? ''));
            $kelasName = trim((string) ($row[$headerMap['kelas']] ?? ''));
            $rawBirthDate = isset($headerMap['birth_date']) ? trim((string) ($row[$headerMap['birth_date']] ?? '')) : '';

            // Ignore empty rows
            if (! $nama && ! $nis && ! $email) {
                continue;
            }

            $errors = [];
            if (! $nama) {
                $errors[] = 'Nama wajib diisi.';
            }
            if (! $nis) {
                $errors[] = 'NIS wajib diisi.';
            } elseif (in_array($nis, $existingNis)) {
                $errors[] = "NIS '{$nis}' sudah terdaftar.";
            }

            if (! $email) {
                $errors[] = 'Email wajib diisi.';
            } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid.';
            } elseif (in_array(strtolower($email), array_map('strtolower', $existingEmails))) {
                $errors[] = "Email '{$email}' sudah digunakan.";
            }

            $matchedClass = $classes[strtolower($kelasName)] ?? null;
            if (! $matchedClass) {
                $errors[] = "Kelas '{$kelasName}' tidak ditemukan di sistem.";
            }

            // Parse Birth Date & Auto-generate Password
            $parsedBirthDate = null;
            $autoPassword = $nis; // Fallback to NIS if birth date is not available
            $passwordSource = 'NIS';

            if (! empty($rawBirthDate)) {
                $extractedDate = $this->parseBirthDateString($rawBirthDate);
                if ($extractedDate) {
                    $parsedBirthDate = $extractedDate->format('Y-m-d');
                    $autoPassword = $extractedDate->format('dmY'); // e.g. 15082008
                    $passwordSource = 'Tgl Lahir ('.$autoPassword.')';
                } else {
                    $errors[] = "Format tanggal lahir '{$rawBirthDate}' tidak valid. Gunakan format DD-MM-YYYY atau YYYY-MM-DD.";
                }
            }

            $isValid = empty($errors);
            if ($isValid) {
                $validCount++;
            } else {
                $errorCount++;
            }

            $parsedRows[] = [
                'row_index' => $index,
                'nama' => $nama,
                'nis' => $nis,
                'email' => $email,
                'kelas_nama' => $kelasName,
                'class_id' => $matchedClass ? $matchedClass->id : null,
                'birth_date' => $parsedBirthDate,
                'raw_birth_date' => $rawBirthDate,
                'auto_password' => $autoPassword,
                'password_source' => $passwordSource,
                'is_valid' => $isValid,
                'errors' => $errors,
            ];
        }

        // Store in session for final commit
        Session::put('student_import_preview', $parsedRows);

        return view('admin.students.import', [
            'previewRows' => $parsedRows,
            'validCount' => $validCount,
            'errorCount' => $errorCount,
            'classes' => SchoolClass::all(),
        ]);
    }

    public function commit(Request $request)
    {
        $previewRows = Session::get('student_import_preview');

        if (empty($previewRows)) {
            return redirect()->route('admin.students.import')->with('error', 'Tidak ada data import yang sedang ditinjau.');
        }

        $importedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($previewRows as $item) {
                if (! $item['is_valid']) {
                    continue;
                }

                $passwordPlain = ! empty($item['auto_password']) ? $item['auto_password'] : $item['nis'];

                $user = User::create([
                    'name' => $item['nama'],
                    'email' => $item['email'],
                    'password' => Hash::make($passwordPlain),
                    'role' => 'siswa',
                    'status' => 'active',
                ]);

                $user->syncRoles('siswa');

                Student::create([
                    'user_id' => $user->id,
                    'nis' => $item['nis'],
                    'class_id' => $item['class_id'],
                    'birth_date' => $item['birth_date'] ?? null,
                ]);

                $importedCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat memproses import: '.$e->getMessage());
        }

        Session::forget('student_import_preview');

        AuditLog::record(Auth::id(), 'import_students', "Berhasil mengimpor {$importedCount} data siswa baru.");

        return redirect()->route('admin.users.index', ['role' => 'siswa'])
            ->with('success', "Berhasil mengimpor {$importedCount} siswa ke dalam sistem. Kata sandi akun otomatis diatur sesuai tanggal lahir (DDMMYYYY) atau nomor NIS.");
    }

    /**
     * Helper to safely parse various date formats from Excel/CSV (Numeric timestamp, Y-m-d, d-m-Y, d/m/Y, dmY).
     */
    protected function parseBirthDateString(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Numeric Excel date serial number (e.g. 39675)
        if (is_numeric($value) && (int) $value > 10000 && strlen($value) === 5) {
            try {
                $dt = ExcelDate::excelToDateTimeObject((float) $value);

                return Carbon::instance($dt);
            } catch (\Exception $e) {
                // fall through
            }
        }

        // Standard 8 digits DDMMYYYY or YYYYMMDD
        if (preg_match('/^(\d{2})(\d{2})(\d{4})$/', $value, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];
            if (checkdate($month, $day, $year)) {
                return Carbon::createFromDate($year, $month, $day);
            }
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $value, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            $day = (int) $matches[3];
            if (checkdate($month, $day, $year)) {
                return Carbon::createFromDate($year, $month, $day);
            }
        }

        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
            'd.m.Y',
            'Y.m.d',
        ];

        foreach ($formats as $fmt) {
            try {
                $parsed = Carbon::createFromFormat($fmt, $value);
                if ($parsed && $parsed->format($fmt) === $value) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
