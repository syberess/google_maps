<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Firma Arama</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            text-align: center;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .help-text {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 13px;
        }
        
        .icon {
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗺️ Google Maps Firma Arama</h1>
        <p class="subtitle">Belirttiğiniz kriterlere göre firma bilgilerini arayın</p>
        
        <form action="{{ route('company.search') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="keyword">
                    <span class="icon">🔍</span>
                    Arama Kelimesi
                </label>
                <input 
                    type="text" 
                    id="keyword" 
                    name="keyword" 
                    placeholder="Örn: Restoran, Kafe, Market" 
                    required
                >
                <div class="help-text">Aramak istediğiniz firma türünü girin</div>
            </div>
            
            <div class="form-group">
                <label for="location">
                    <span class="icon">📍</span>
                    Konum
                </label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    placeholder="Örn: İstanbul, Ankara, İzmir" 
                    required
                >
                <div class="help-text">Aramak istediğiniz şehir veya bölge</div>
            </div>
            
            <div class="form-group">
                <label for="radius">
                    <span class="icon">📏</span>
                    Yarıçap (metre)
                </label>
                <input 
                    type="number" 
                    id="radius" 
                    name="radius" 
                    value="5000" 
                    min="100" 
                    max="50000"
                    required
                >
                <div class="help-text">Arama yapılacak alan (100-50000 metre arası)</div>
            </div>
            
            <button type="submit">
                🚀 Aramaya Başla
            </button>
        </form>
        
        <div class="footer">
            <p>Google Maps API kullanılarak geliştirilmiştir</p>
        </div>
    </div>
</body>
</html>
