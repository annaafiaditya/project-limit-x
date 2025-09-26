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
            $entries = $table->entries()->orderBy('id')->get();
            
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
                
                // Header styling - Judul utama
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('dbeafe');
                
                // Styling untuk setiap tabel - hanya garis tanpa styling lain
                $currentRow = 6;
                $tables = $this->form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
                
                foreach ($tables as $index => $table) {
                    // Nama tabel tanpa styling
                    $currentRow += 2;
                    
                    $columns = $table->columns;
                    $colCount = count($columns);
                    $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                    $entryCount = $table->entries()->count();
                    
                    // Header tabel tanpa styling
                    
                    if ($entryCount > 0) {
                        $dataEnd = $currentRow + $entryCount;
                        $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
                    } else {
                        $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                    }
                    
                    $currentRow += $entryCount + 2;
                }
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(15);
            }
        ];
    }
    
    
    public function title(): string
    {
        return 'Form_'.$this->form->id;
    }
}
