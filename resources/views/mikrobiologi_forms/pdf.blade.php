<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mikrobiologi_form->title }} - {{ $mikrobiologi_form->no }}</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            margin: 0 0 10px 0;
        }
        
        .form-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f0fdf4;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        
        .form-info div {
            flex: 1;
        }
        
        .form-info strong {
            color: #059669;
        }
        
        .table-section {
            margin-bottom: 30px;
        }
        
        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 15px;
            padding: 10px;
            background: #d1fae5;
            border-radius: 6px;
            border-left: 4px solid #10b981;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        th {
            background: #10b981;
            color: white;
            font-weight: bold;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #059669;
        }
        
        td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        
        tr:nth-child(even) {
            background: #f0fdf4;
        }
        
        tr:hover {
            background: #d1fae5;
        }
        
        .approval-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .approval-title {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 15px;
            padding: 10px;
            background: #d1fae5;
            border-radius: 6px;
            border-left: 4px solid #10b981;
        }
        
        .approval-table th {
            background: #10b981;
            border: 1px solid #059669;
        }
        
        .status-accept {
            color: #059669;
            font-weight: bold;
        }
        
        .status-reject {
            color: #dc2626;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .no-data {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 20px;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $mikrobiologi_form->title }}</h1>
        <div style="font-size: 14px; color: #6b7280;">
            Form Mikrobiologi - LIMIT-X
        </div>
    </div>

    <div class="form-info">
        <div>
            <strong>No Form:</strong> {{ $mikrobiologi_form->no }}
        </div>
        <div>
            <strong>Tanggal Inokulasi:</strong> {{ \Carbon\Carbon::parse($mikrobiologi_form->tgl_inokulasi)->format('d/m/Y') }}
        </div>
        <div>
            <strong>Tanggal Pengamatan:</strong> {{ \Carbon\Carbon::parse($mikrobiologi_form->tgl_pengamatan)->format('d/m/Y') }}
        </div>
        <div>
            <strong>Dibuat:</strong> {{ \Carbon\Carbon::parse($mikrobiologi_form->created_at)->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="table-section">
        <div class="table-title">Data Mikrobiologi</div>
        
        @if($entries->count() > 0)
            <table>
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th>{{ $col->nama_kolom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            @foreach($columns as $col)
                                <td>
                                    @if(isset($entry->data[$col->id]))
                                        @if($col->tipe_kolom === 'date')
                                            {{ \Carbon\Carbon::parse($entry->data[$col->id])->format('d/m/Y') }}
                                        @elseif($col->tipe_kolom === 'time')
                                            {{ $entry->data[$col->id] }}
                                        @elseif($col->tipe_kolom === 'integer' && is_numeric($entry->data[$col->id]))
                                            {{ number_format($entry->data[$col->id]) }}
                                        @else
                                            {{ $entry->data[$col->id] }}
                                        @endif
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data entry untuk form ini
            </div>
        @endif
    </div>

    <div class="approval-section">
        <div class="approval-title">Approval / Signature</div>
        
        @if($signatures->count() > 0)
            <table class="approval-table">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($signatures as $sig)
                        <tr>
                            <td>
                                @if($sig->role === 'technician')
                                    QA Lab. Technician
                                @elseif($sig->role === 'staff')
                                    QA Staff
                                @elseif($sig->role === 'supervisor')
                                    QA Supervisor
                                @else
                                    {{ ucfirst($sig->role) }}
                                @endif
                            </td>
                            <td>{{ $sig->name }}</td>
                            <td>{{ $sig->jabatan }}</td>
                            <td class="{{ $sig->status === 'accept' ? 'status-accept' : 'status-reject' }}">
                                {{ ucfirst($sig->status) }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sig->tanggal)->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Belum ada approval/signature
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem LIMIT-X</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
