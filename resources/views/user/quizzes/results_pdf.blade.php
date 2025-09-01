<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Asesmen - {{ $quiz->name }}</title>
    <style>
        /* [IMPROVISASI] CSS dirancang ulang untuk tampilan laporan yang lebih modern dan profesional */
        @page {
            margin: 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 11px;
            background-color: #fff;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: left;
            margin-bottom: 25px;
            border-bottom: 3px solid #007BFF;
            padding-bottom: 15px;
        }
        .header .brand {
            font-size: 28px;
            font-weight: bold;
            color: #007BFF;
        }
        .header .title {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007BFF;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .user-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .user-info table {
            width: 100%;
        }
        .user-info td {
            padding: 4px 0;
            vertical-align: top;
        }
        .user-info td:first-child {
            width: 100px;
        }
        .insight-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007BFF;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            padding: 15px;
            border-radius: 5px;
            font-style: italic;
        }
        .score-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .score-header {
            display: block;
            margin-bottom: 10px;
        }
        .score-header .subscale-name {
            font-size: 14px;
            font-weight: bold;
        }
        .score-header .interpretation-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            float: right;
        }
        .score-bar-container {
            background-color: #e9ecef;
            border-radius: 5px;
            height: 8px;
            width: 100%;
        }
        .score-bar {
            height: 8px;
            border-radius: 5px;
        }
        .score-value {
            text-align: right;
            font-size: 10px;
            color: #555;
            margin-top: 4px;
        }
        .disclaimer {
            margin-top: 30px;
            padding: 15px;
            background-color: #fffbe6;
            border: 1px solid #ffe58f;
            border-left: 4px solid #facc15;
            font-size: 10px;
            color: #7a6200;
            border-radius: 5px;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        /* Color Classes */
        .color-green { color: #15803d; } .bg-green { background-color: #22c55e; }
        .color-yellow { color: #a16207; } .bg-yellow { background-color: #f59e0b; }
        .color-red { color: #b91c1c; } .bg-red { background-color: #ef4444; }
        .color-gray { color: #4b5563; } .bg-gray { background-color: #6b7280; }
    </style>
</head>
<body>
    <div class="footer">
        Dokumen ini dibuat oleh <strong>RelaxBoss</strong> &copy; {{ date('Y') }}. Informasi bersifat rahasia.
    </div>

    <div class="container">
        <div class="header">
            <div class="brand">RelaxBoss</div>
            <div class="title">Laporan Hasil Asesmen Kesejahteraan</div>
        </div>

        <div class="user-info">
            <table>
                <tr>
                    <td><strong>Nama</strong></td>
                    <td>: {{ $attempt->user->name }}</td>
                </tr>
                <tr>
                    <td><strong>Asesmen</strong></td>
                    <td>: {{ $quiz->name }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal</strong></td>
                    <td>: {{ $attempt->created_at->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">Ringkasan Medis (AI Summary)</h2>
            <div class="summary-box">
                <p>"{{ $attempt->ai_summary ?? 'Ringkasan tidak tersedia.' }}"</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Rekomendasi Personal (AI Recommendation)</h2>
            <div class="insight-box">
                <p>{!! $attempt->ai_recommendation ? nl2br(e($attempt->ai_recommendation)) : 'Rekomendasi tidak tersedia.' !!}</p>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">Rincian Skor</h2>
            @foreach ($results as $subScale => $result)
                @php
                    $interpretation = $result['interpretation'];
                    $color = match(strtolower($interpretation)) {
                        'normal', 'ringan' => 'green',
                        'sedang' => 'yellow',
                        'parah', 'sangat parah', 'tinggi' => 'red',
                        default => 'gray',
                    };
                    $percentage = $result['max_score'] > 0 ? ($result['score'] / $result['max_score']) * 100 : 0;
                @endphp
                <div class="score-card">
                    <div class="score-header">
                        <span class="interpretation-badge bg-{{$color}}">{{ $interpretation }}</span>
                        <div class="subscale-name">{{ ucfirst($subScale) }}</div>
                    </div>
                    <div class="score-bar-container">
                        <div class="score-bar bg-{{$color}}" style="width: {{ $percentage }}%;"></div>
                    </div>
                    <div class="score-value">
                        Skor: <strong>{{ $result['score'] }}</strong> / {{ $result['max_score'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="disclaimer">
            <strong>Penting:</strong> Hasil asesmen ini bukan merupakan diagnosis medis. Ini adalah alat bantu untuk refleksi diri. Jika Anda merasa khawatir dengan kondisi Anda, sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental.
        </div>
    </div>
</body>
</html>
