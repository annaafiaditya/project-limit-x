<?php

namespace App\Exports;

use App\Models\MikrobiologiForm;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormExport implements FromArray, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $form;
    
    public function __construct(MikrobiologiForm $form)
    {
        $this->form = $form;
    }
    
    public function array(): array
    {
        $header = [
            [$this->form->title],
            ['No Form', $this->form->no],
            ['Tanggal Inokulasi', $this->form->tgl_inokulasi],
            ['Tanggal Pengamatan', $this->form->tgl_pengamatan],
            [''],
        ];
        
        $columns = $this->form->columns()->orderBy('urutan')->get();
        $entries = $this->form->entries;
        $approval = $this->form->signatures;
        
        $tableHeader = $columns->map(fn($col) => $col->nama_kolom)->toArray();
        $tableRows = [];
        
        foreach ($entries as $entry) {
            $row = [];
            foreach ($columns as $col) {
                $value = $entry->data[$col->id] ?? '';
                
                // Format data berdasarkan tipe kolom
                if ($col->tipe_kolom === 'date' && $value) {
                    $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                } elseif ($col->tipe_kolom === 'time' && $value) {
                    $value = $value;
                } elseif ($col->tipe_kolom === 'integer' && is_numeric($value)) {
                    $value = number_format($value);
                }
                
                $row[] = $value;
            }
            $tableRows[] = $row;
        }
        
        $approvalRows = [
            [''],
            ['Approval / Signature'],
            ['Role', 'Nama', 'Jabatan', 'Status', 'Tanggal'],
        ];
        
        foreach ($approval as $sig) {
            $role = match($sig->role) {
                'technician' => 'QA Lab. Technician',
                'staff' => 'QA Staff',
                'supervisor' => 'QA Supervisor',
                default => ucfirst($sig->role)
            };
            
            $approvalRows[] = [
                $role,
                $sig->name,
                $sig->jabatan,
                ucfirst($sig->status),
                \Carbon\Carbon::parse($sig->tanggal)->format('d/m/Y'),
            ];
        }
        
        return array_merge($header, [$tableHeader], $tableRows, $approvalRows);
    }
    
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Judul besar dan merge cell
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('dbeafe');
        
        // Info form
        $sheet->getStyle('A2:B4')->getFont()->setBold(true);
        $sheet->getStyle('A2:B4')->getFill()->setFillType('solid')->getStartColor()->setRGB('f8fafc');
        
        // Hitung posisi baris header dan data entry
        $headerRow = 6;
        $columns = $this->form->columns()->orderBy('urutan')->get();
        $colCount = count($columns);
        $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
        $entryCount = $this->form->entries->count();
        $dataStart = $headerRow + 1;
        $dataEnd = $headerRow + $entryCount;
        
        // Styling untuk header tabel
        $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('10b981');
        $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getAlignment()->setHorizontal('center');
        
        if ($entryCount > 0) {
            $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
            // Alternating row colors
            for ($i = $dataStart; $i <= $dataEnd; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle('A'.$i.':'.$colLetterEnd.$i)->getFill()->setFillType('solid')->getStartColor()->setRGB('f0fdf4');
                }
            }
        } else {
            $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
        }
        
        // Spasi antar section
        $sheet->getRowDimension(5)->setRowHeight(15);
        
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Header styling
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('dbeafe');
                
                // Form info styling
                $sheet->getStyle('A2:B4')->getFont()->setBold(true);
                $sheet->getStyle('A2:B4')->getFill()->setFillType('solid')->getStartColor()->setRGB('f8fafc');
                
                // Table styling
                $columns = $this->form->columns()->orderBy('urutan')->get();
                $colCount = count($columns);
                $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                $headerRow = 6;
                $entryCount = $this->form->entries->count();
                $dataStart = $headerRow + 1;
                $dataEnd = $headerRow + $entryCount;
                
                // Header table
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('10b981');
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                
                if ($entryCount > 0) {
                    $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
                    // Alternating row colors
                    for ($i = $dataStart; $i <= $dataEnd; $i++) {
                        if ($i % 2 == 0) {
                            $sheet->getStyle('A'.$i.':'.$colLetterEnd.$i)->getFill()->setFillType('solid')->getStartColor()->setRGB('f0fdf4');
                        }
                    }
                } else {
                    $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                }
                
                // Approval section styling
                $approvalStartRow = $headerRow + $entryCount + 3;
                $sheet->getStyle('A'.$approvalStartRow)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.$approvalStartRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('d1fae5');
                
                $approvalHeaderRow = $approvalStartRow + 1;
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('10b981');
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getAlignment()->setHorizontal('center');
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(15);
                $sheet->getRowDimension($approvalStartRow)->setRowHeight(20);
            }
        ];
    }
    
    public function title(): string
    {
        return 'Form_'.$this->form->id;
    }
} 