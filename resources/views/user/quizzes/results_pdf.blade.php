<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Asesmen - {{ $quiz->name }}</title>
    <style>
        /* CSS ini dirancang agar ramah untuk konversi ke PDF */
        @page {
            margin: 25px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 12px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #007BFF;
            margin: 0;
            font-size: 24px;
        }
        .section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .section h2 {
            font-size: 16px;
            color: #007BFF;
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .user-info {
            margin-bottom: 20px;
            font-size: 11px;
        }
        .user-info td {
            padding: 2px 5px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .results-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .disclaimer {
            margin-top: 20px;
            padding: 10px;
            background-color: #fffbe6;
            border-left: 4px solid #facc15;
            font-size: 10px;
            color: #7a6200;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="footer">
        Dokumen ini dibuat oleh RelaxBoss &copy; {{ date('Y') }}
    </div>

    <div class="container">
        <div class="header">
            <h1>RelaxBoss</h1>
            <p style="margin: 0; font-size: 14px;">Laporan Hasil Asesmen Kesejahteraan</p>
        </div>

        <table class="user-info">
            <tr>
                <td><strong>Nama:</strong></td>
                <td>{{ $attempt->user->name }}</td>
            </tr>
            <tr>
                <td><strong>Asesmen:</strong></td>
                <td>{{ $quiz->name }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal:</strong></td>
                <td>{{ $attempt->created_at->format('d F Y, H:i') }}</td>
            </tr>
        </table>

        <div class="section">
            <h2>Ringkasan Medis (AI Summary)</h2>
            <p><i>"{{ $attempt->ai_summary ?? 'Ringkasan tidak tersedia.' }}"</i></p>
        </div>

        <div class="section">
            <h2>Rekomendasi Personal (AI Recommendation)</h2>
            <p>{{ $attempt->ai_recommendation ?? 'Rekomendasi tidak tersedia.' }}</p>
        </div>

        @if($attempt->user_context)
        <div class="section">
            <h2>Konteks dari Pengguna</h2>
            <p><i>"{{ $attempt->user_context }}"</i></p>
        </div>
        @endif

        <div class="section">
            <h2>Rincian Skor</h2>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Area Asesmen (Sub-Skala)</th>
                        <th>Skor</th>
                        <th>Interpretasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $subScale => $result)
                        <tr>
                            <td>{{ ucfirst($subScale) }}</td>
                            <td>{{ $result['score'] }}</td>
                            <td><strong>{{ $result['interpretation'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="disclaimer">
            <strong>Penting:</strong> Hasil asesmen ini bukan merupakan diagnosis medis. Ini adalah alat bantu untuk refleksi diri. Jika Anda merasa khawatir dengan kondisi Anda, sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental.
        </div>
    </div>
</body>
</html>
