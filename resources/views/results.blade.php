<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama Sonuçları - Google Maps</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 28px;
        }
        
        .search-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
        }
        
        .info-item strong {
            color: #333;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .results-container {
            background: white;
            padding: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .results-count {
            font-size: 18px;
            color: #333;
            font-weight: 600;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .result-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 2px solid transparent;
        }
        
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .card-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #ff9800;
            font-weight: 600;
        }
        
        .card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .info-row {
            display: flex;
            align-items: start;
            gap: 10px;
            font-size: 14px;
            color: #555;
        }
        
        .info-icon {
            min-width: 20px;
            text-align: center;
        }
        
        .info-text {
            flex: 1;
            word-break: break-word;
        }
        
        .link {
            color: #667eea;
            text-decoration: none;
        }
        
        .link:hover {
            text-decoration: underline;
        }
        
        .types-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .tag {
            background: #e0e7ff;
            color: #667eea;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-results-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .results-grid {
                grid-template-columns: 1fr;
            }
            
            .search-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗺️ Arama Sonuçları</h1>
            
            <div class="search-info">
                <div class="info-item">
                    <span>🔍</span>
                    <strong>Arama:</strong>
                    <span>{{ $keyword }}</span>
                </div>
                <div class="info-item">
                    <span>📍</span>
                    <strong>Konum:</strong>
                    <span>{{ $location }}</span>
                </div>
                <div class="info-item">
                    <span>📏</span>
                    <strong>Yarıçap:</strong>
                    <span>{{ number_format($radius) }} metre</span>
                </div>
            </div>
            
            <div class="actions">
                <a href="/" class="btn btn-secondary">
                    ⬅️ Yeni Arama
                </a>
                @if(count($results) > 0)
                    <a href="{{ route('company.export') }}" class="btn btn-success">
                        📥 Excel'e Aktar ({{ count($results) }} firma)
                    </a>
                @endif
            </div>
        </div>
        
        <div class="results-container">
            @if(count($results) > 0)
                <div class="results-header">
                    <div class="results-count">
                        📊 Toplam <strong>{{ count($results) }}</strong> firma bulundu
                    </div>
                </div>
                
                <div class="results-grid">
                    @foreach($results as $result)
                        <div class="result-card">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">{{ $result['name'] }}</div>
                                    @if($result['rating'] !== 'N/A')
                                        <div class="card-rating">
                                            ⭐ {{ $result['rating'] }}
                                            @if($result['total_ratings'] !== 'N/A')
                                                <span style="color: #999;">({{ $result['total_ratings'] }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="info-row">
                                    <span class="info-icon">📍</span>
                                    <span class="info-text">{{ $result['address'] }}</span>
                                </div>
                                
                                @if($result['phone'] !== 'N/A')
                                    <div class="info-row">
                                        <span class="info-icon">📞</span>
                                        <span class="info-text">
                                            <a href="tel:{{ $result['phone'] }}" class="link">{{ $result['phone'] }}</a>
                                        </span>
                                    </div>
                                @endif
                                
                                @if($result['website'] !== 'N/A')
                                    <div class="info-row">
                                        <span class="info-icon">🌐</span>
                                        <span class="info-text">
                                            <a href="{{ $result['website'] }}" target="_blank" class="link">Web Sitesi</a>
                                        </span>
                                    </div>
                                @endif
                                
                                @if($result['latitude'] !== 'N/A' && $result['longitude'] !== 'N/A')
                                    <div class="info-row">
                                        <span class="info-icon">🗺️</span>
                                        <span class="info-text">
                                            <a href="https://www.google.com/maps?q={{ $result['latitude'] }},{{ $result['longitude'] }}" 
                                               target="_blank" 
                                               class="link">
                                                Haritada Göster
                                            </a>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2>Sonuç Bulunamadı</h2>
                    <p>Arama kriterlerinize uygun firma bulunamadı. Lütfen farklı bir arama deneyin.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
