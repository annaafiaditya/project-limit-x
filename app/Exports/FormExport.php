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
                $row[] = $entry->data[$col->id] ?? '';
            }
            $tableRows[] = $row;
        }
        $approvalRows = [
            [''],
            ['Approval / Signature'],
            ['Role', 'Nama', 'Status', 'Tanggal'],
        ];
        foreach ($approval as $sig) {
            $approvalRows[] = [
                $sig->role,
                $sig->name,
                $sig->status,
                $sig->tanggal,
            ];
        }
        return array_merge($header, [$tableHeader], $tableRows, $approvalRows);
    }
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Judul besar dan merge cell
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        // Info form
        $sheet->getStyle('A2:B4')->getFont()->setBold(true);
        // Hitung posisi baris header dan data entry
        $headerRow = 6;
        $columns = $this->form->columns()->orderBy('urutan')->get();
        $colCount = count($columns);
        $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
        $entryCount = $this->form->entries->count();
        $dataStart = $headerRow + 1;
        $dataEnd = $headerRow + $entryCount;
        // Styling hanya untuk header dan data entry
        $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('cfe2ff');
        if ($entryCount > 0) {
            $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
        } else {
            $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
        }
        // Approval section polos
        // Spasi antar section
        $sheet->getRowDimension(5)->setRowHeight(10);
        return [];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                // Styling hanya untuk tabel data entry
                $columns = $this->form->columns()->orderBy('urutan')->get();
                $colCount = count($columns);
                $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                $headerRow = 6;
                $entryCount = $this->form->entries->count();
                $dataStart = $headerRow + 1;
                $dataEnd = $headerRow + $entryCount;
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('cfe2ff');
                if ($entryCount > 0) {
                    $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
                } else {
                    $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                }
            }
        ];
    }
    public function title(): string
    {
        return 'Form_'.$this->form->id;
    }
} 