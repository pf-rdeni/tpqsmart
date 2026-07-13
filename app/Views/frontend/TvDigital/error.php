<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Error TV Digital' ?></title>
    <!-- Google Font: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 100%);
            color: #ffffff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px 30px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        .error-icon {
            font-size: 72px;
            color: #e74c3c;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        h1 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }
        p {
            color: #b0b0c5;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-home {
            display: inline-block;
            text-decoration: none;
            background: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }
        .btn-home:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.6);
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Link Tidak Valid / Mati</h1>
        <p>Maaf, link TV Digital / Digital Signage ini tidak ditemukan atau sudah dinonaktifkan oleh Operator TPQ. Pastikan kembali token HashKey yang Anda masukkan sudah benar.</p>
        <a href="<?= base_url() ?>" class="btn-home"><i class="fas fa-home"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
