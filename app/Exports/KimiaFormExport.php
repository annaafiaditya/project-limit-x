<?php

namespace App\Exports;

use App\Models\KimiaForm;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KimiaFormExport implements FromArray, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $form;
    
    public function __construct(KimiaForm $form)
    {
        $this->form = $form;
    }
    
    public function array(): array
    {
        $header = [
            [$this->form->title],
            ['No Form', $this->form->no],
            ['Tanggal', $this->form->tanggal],
            [''],
        ];
        
        $allData = [];
        $tables = $this->form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        
        foreach ($tables as $table) {
            $allData[] = [$table->name];
            $allData[] = [''];
            
            $columns = $table->columns;
            $entries = $table->entries;
            
            $tableHeader = $columns->map(fn($col) => $col->nama_kolom)->toArray();
            $allData[] = $tableHeader;
            
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
                    } elseif ($col->tipe_kolom === 'decimal' && is_numeric($value)) {
                        $value = number_format($value, 2);
                    } elseif ($col->tipe_kolom === 'integer' && is_numeric($value)) {
                        $value = number_format($value);
                    }
                    
                    $row[] = $value;
                }
                $tableRows[] = $row;
            }
            
            $allData = array_merge($allData, $tableRows);
            $allData[] = [''];
        }
        
        $approvalRows = [
            ['Approval / Signature'],
            ['Role', 'Nama', 'Jabatan', 'Status', 'Tanggal'],
        ];
        
        $approval = $this->form->signatures;
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
        
        return array_merge($header, $allData, $approvalRows);
    }
    
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Judul besar dan merge cell
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('dbeafe');
        
        // Info form
        $sheet->getStyle('A2:B3')->getFont()->setBold(true);
        $sheet->getStyle('A2:B3')->getFill()->setFillType('solid')->getStartColor()->setRGB('f8fafc');
        
        // Styling untuk setiap tabel dengan warna berbeda
        $currentRow = 6;
        $tables = $this->form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $colors = ['3b82f6', '8b5cf6', '06b6d4', '10b981', 'f59e0b', 'ef4444', '84cc16', 'f97316'];
        
        foreach ($tables as $index => $table) {
            $color = $colors[$index % count($colors)];
            $lightColor = $this->getLightColor($color);
            
            // Nama tabel
            $sheet->getStyle('A'.$currentRow)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A'.$currentRow)->getFill()->setFillType('solid')->getStartColor()->setRGB($lightColor);
            $currentRow += 2;
            
            $columns = $table->columns;
            $colCount = count($columns);
            $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
            $entryCount = $table->entries->count();
            
            // Header tabel dengan warna yang berbeda
            $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getFill()->setFillType('solid')->getStartColor()->setRGB($color);
            $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getAlignment()->setHorizontal('center');
            
            if ($entryCount > 0) {
                $dataEnd = $currentRow + $entryCount;
                $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
                
                // Alternating row colors dengan warna yang lebih light
                for ($i = $currentRow + 1; $i <= $dataEnd; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A'.$i.':'.$colLetterEnd.$i)->getFill()->setFillType('solid')->getStartColor()->setRGB($lightColor);
                    }
                }
            } else {
                $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
            }
            
            $currentRow += $entryCount + 2;
        }
        
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
                $sheet->getStyle('A2:B3')->getFont()->setBold(true);
                $sheet->getStyle('A2:B3')->getFill()->setFillType('solid')->getStartColor()->setRGB('f8fafc');
                
                // Styling untuk setiap tabel dengan warna berbeda
                $currentRow = 6;
                $tables = $this->form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
                $colors = ['3b82f6', '8b5cf6', '06b6d4', '10b981', 'f59e0b', 'ef4444', '84cc16', 'f97316'];
                
                foreach ($tables as $index => $table) {
                    $color = $colors[$index % count($colors)];
                    $lightColor = $this->getLightColor($color);
                    
                    // Nama tabel
                    $sheet->getStyle('A'.$currentRow)->getFont()->setBold(true)->setSize(14);
                    $sheet->getStyle('A'.$currentRow)->getFill()->setFillType('solid')->getStartColor()->setRGB($lightColor);
                    $currentRow += 2;
                    
                    $columns = $table->columns;
                    $colCount = count($columns);
                    $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                    $entryCount = $table->entries->count();
                    
                    // Header tabel dengan warna yang berbeda
                    $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getFill()->setFillType('solid')->getStartColor()->setRGB($color);
                    $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                    
                    if ($entryCount > 0) {
                        $dataEnd = $currentRow + $entryCount;
                        $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
                        
                        // Alternating row colors dengan warna yang lebih light
                        for ($i = $currentRow + 1; $i <= $dataEnd; $i++) {
                            if ($i % 2 == 0) {
                                $sheet->getStyle('A'.$i.':'.$colLetterEnd.$i)->getFill()->setFillType('solid')->getStartColor()->setRGB($lightColor);
                            }
                        }
                    } else {
                        $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                    }
                    
                    $currentRow += $entryCount + 2;
                }
                
                // Approval section styling
                $approvalStartRow = $currentRow;
                $sheet->getStyle('A'.$approvalStartRow)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.$approvalStartRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('d1fae5');
                
                $approvalHeaderRow = $approvalStartRow + 1;
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('3b82f6');
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle('A'.$approvalHeaderRow.':E'.$approvalHeaderRow)->getAlignment()->setHorizontal('center');
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(15);
                $sheet->getRowDimension($approvalStartRow)->setRowHeight(20);
            }
        ];
    }
    
    private function getLightColor($color)
    {
        $lightColors = [
            '3b82f6' => 'dbeafe', // blue
            '8b5cf6' => 'e9d5ff', // purple
            '06b6d4' => 'cffafe', // cyan
            '10b981' => 'd1fae5', // emerald
            'f59e0b' => 'fef3c7', // amber
            'ef4444' => 'fee2e2', // red
            '84cc16' => 'f7fee7', // lime
            'f97316' => 'fed7aa', // orange
        ];
        
        return $lightColors[$color] ?? 'f8fafc';
    }
    
    public function title(): string
    {
        return 'Form_'.$this->form->id;
    }
}
