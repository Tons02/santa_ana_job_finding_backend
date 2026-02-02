<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    protected $status;
    protected $filters;

    public function __construct($status = null, $filters = [])
    {
        $this->status = $status;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with('skills', 'courses', 'preferred_positions')
            ->when($this->status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->get();
    }

    /**
     * Map data for each row
     */
    public function map($user): array
    {
        if ($user->date_of_birth) {
            $diff = Carbon::parse($user->date_of_birth)->diff(now());
            $age = "{$diff->y} years {$diff->m} months";
        } else {
            $age = null;
        }

        $createdAtFormatted = $user->created_at
            ? Carbon::parse($user->created_at)->format('m/d/Y g:i:s A')
            : null;

        $updatedAtFormatted = $user->updated_at
            ? Carbon::parse($user->updated_at)->format('m/d/Y g:i:s A')
            : null;

        $religion = match ($user->religion) {
            'roman_catholic' => 'Roman Catholic',
            'islam' => 'Islam',
            'iglesia_ni_cristo' => 'Iglesia ni Cristo',
            'born_again' => 'Born Again',
            'baptist' => 'Baptist',
            'seventh_day_adventist' => 'Seventh Day Adventist',
            default => $user->religion
        };

        $employment_status = match ($user->employment_status) {
            'employed' => 'Employed',
            'unemployed' => 'Unemployed',
            'self_employed' => 'Self Employed',
            default => $user->employment_status
        };

        $employment_type = match ($user->employment_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'self_employed' => 'Self Employed',
            'freelance' => 'Freelance',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'wage' => 'Wage',
            default => $user->employment_type
        };

        return [
            'SANTA ANA (PAMPANGA)',
            'Municipality',
            '3rd Class',
            $user->id,
            $user->full_name,
            $user->first_name,
            $user->middle_name,
            $user->last_name,
            $user->suffix,
            $user->date_of_birth,
            $age,
            $user->gender,
            $user->civil_status,
            $user->full_address,
            $user->region,
            $user->province,
            $user->city_municipality,
            $user->barangay,
            $user->street_address,
            $user->telephone,
            ' ' . $user->mobile_number,
            $user->height,
            $religion,
            $employment_status,
            $employment_type,
            $user->months_looking,
            $user->is_4ps ? 'Yes' : 'No',
            $user->is_pwd ? 'Yes' : 'No',
            $user->disability,
            $user->is_ofw ? 'Yes' : 'No',
            $user->work_experience,
            $user->country,
            $user->is_former_ofw ? 'Yes' : 'No',
            $user->last_deployment,
            $user->return_date,
            $user->transaction_date,
            $user->program_service,
            $user->event,
            $user->transaction,
            $user->remarks,
            $user->email,
            $user->username,
            $user->role_type,
            $user->skills->pluck('name')->join(', '),
            $user->courses->pluck('name')->join(', '),
            $user->preferred_positions->pluck('name')->join(', '),
            $createdAtFormatted,
            $updatedAtFormatted,
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'PESO',
            'AREA TYPE',
            'AREA CLASS',
            'ID',
            'Name',
            'First Name',
            'Middle Name',
            'Last Name',
            'Suffix',
            'Date of Birth',
            'Age',
            'Gender',
            'Civil Status',
            'Full Address',
            'Region',
            'Province',
            'City/Municipality',
            'Barangay',
            'Street Address',
            'Telephone',
            'Mobile Number',
            'Height',
            'Religion',
            'Employment Status',
            'Employment Type',
            'Months Looking',
            '4Ps Beneficiary',
            'PWD',
            'Disability',
            'OFW',
            'Work Experience',
            'Country',
            'Former OFW',
            'Last Deployment',
            'Return Date',
            'Transaction Date',
            'Program/Service',
            'Event',
            'Transaction',
            'Remarks',
            'Email',
            'Username',
            'Role Type',
            'Skills',
            'Courses',
            'Preferred Positions',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 17,
            'C' => 17,
            'D' => 10,
            'E' => 30,
            'F' => 15,
            'G' => 15,
            'H' => 20,
            'I' => 10,
            'J' => 15,
            'K' => 20,
            'L' => 15,
            'M' => 20,
            'N' => 100,
            'O' => 30,
            'P' => 15,
            'Q' => 25,
            'R' => 18,
            'S' => 30,
            'T' => 17,
            'U' => 17,
            'V' => 10,
            'W' => 23,
            'X' => 20,
            'Y' => 22,
            'Z' => 17,
            'AA' => 15,
            'AB' => 10,
            'AC' => 20,
            'AD' => 10,
            'AE' => 20,
            'AF' => 20,
            'AG' => 15,
            'AH' => 20,
            'AI' => 25,
            'AJ' => 17,
            'AK' => 17,
            'AL' => 30,
            'AM' => 30,
            'AN' => 30,
            'AO' => 35,
            'AP' => 20,
            'AQ' => 20,
            'AR' => 100,
            'AS' => 100,
            'AT' => 100,
            'AU' => 22,
            'AV' => 22,
        ];
    }

    /**
     * Style the header row and body
     */
    public function styles(Worksheet $sheet)
    {
        // Get the highest row number (total rows including header)
        $highestRow = $sheet->getHighestRow();

        // Style header row (row 1) - Arial, size 13, bold
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Style body rows (row 2 onwards) - Arial, size 11
        if ($highestRow > 1) {
            $sheet->getStyle('2:' . $highestRow)->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'size' => 10
                ],
            ]);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Format Mobile Number column (U) as text
                $sheet->getStyle('U2:U' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Also format Telephone column (T) as text if needed
                $sheet->getStyle('T2:T' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
            },
        ];
    }
}
