import threading
import os
import re
from flask import Flask, request, jsonify, render_template
import requests
from urllib.parse import urlparse
from internal_service import run_internal_service
from PIL import Image
from pyzbar.pyzbar import decode
from flask_wtf.csrf import CSRFProtect
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address

# Configuración de la aplicación
app = Flask(__name__)
app.config['SECRET_KEY'] = os.environ.get('SECRET_KEY', os.urandom(24))
app.config['MAX_CONTENT_LENGTH'] = 5 * 1024 * 1024  # Limita subidas a 5MB

# Añadir protección CSRF
csrf = CSRFProtect(app)

# Añadir limitador de tasa para prevenir abusos
limiter = Limiter(
    get_remote_address,
    app=app,
    default_limits=["100 per day", "20 per hour"]
)

# Lista de patrones de URL maliciosas conocidas
MALICIOUS_PATTERNS = [
    r'phish', r'hack', r'malware', r'ransom', r'exploit',
    r'backdoor', r'trojan', r'virus', r'steal'
]

@app.route('/')
def home():
    return render_template('index.html')

def is_url_safe(url):
    """Verifica si una URL es segura basándose en varios criterios"""
    try:
        # Comprobar si la URL tiene un formato válido
        parsed_url = urlparse(url)
        if not parsed_url.scheme or not parsed_url.netloc:
            return False, "URL malformada"
        
        # Para fines de laboratorio se elimina la validación de lista blanca.
        # Se sigue comprobando si aparecen patrones maliciosos.
        for pattern in MALICIOUS_PATTERNS:
            if re.search(pattern, url.lower()):
                return False, "URL potencialmente maliciosa (contiene patrón sospechoso)"
                
        return True, "URL segura"
    except Exception as e:
        return False, f"Error al analizar la URL: {str(e)}"

def check_url_safety(url):
    """Analiza una URL verificando su seguridad antes de realizar cualquier solicitud"""
    is_safe, message = is_url_safe(url)
    
    if not is_safe:
        return {
            "status": "warning",
            "message": message,
            "risk_level": "alto"
        }
    
    try:
        session = requests.Session()
        session.headers.update({
            'User-Agent': 'QRSafetyAnalyzer/1.0',
        })
        response = session.get(url, timeout=3, allow_redirects=False)
        status = response.status_code
        content_preview = response.text[:150] if len(response.text) > 0 else "(Sin contenido)"
        risk_level = "bajo"
        if status >= 300 and status < 400:
            risk_level = "medio"
        return {
            "status": "success",
            "http_status": status,
            "content_preview": content_preview,
            "risk_level": risk_level
        }
    except requests.exceptions.Timeout:
        return {
            "status": "warning",
            "message": "La solicitud excedió el tiempo de espera",
            "risk_level": "medio"
        }
    except requests.exceptions.TooManyRedirects:
        return {
            "status": "warning",
            "message": "Demasiadas redirecciones - posible intento de phishing",
            "risk_level": "alto"
        }
    except requests.exceptions.RequestException as e:
        return {
            "status": "error",
            "message": str(e),
            "risk_level": "desconocido"
        }

@app.route('/analyze', methods=['POST'])
@limiter.limit("10 per minute")
def analyze():
    if 'qrimage' not in request.files:
        return jsonify({"error": "No se ha subido ningún archivo"}), 400
    
    file = request.files['qrimage']
    
    if not file.filename or '.' not in file.filename:
        return jsonify({"error": "Archivo inválido"}), 400
    
    extension = file.filename.rsplit('.', 1)[1].lower()
    if extension not in ['jpg', 'jpeg', 'png', 'gif', 'bmp']:
        return jsonify({"error": "Solo se permiten archivos de imagen"}), 400
    
    try:
        image = Image.open(file.stream)
    except Exception as e:
        return jsonify({"error": f"Error al procesar la imagen: {str(e)}"}), 400
    
    try:
        decoded_objects = decode(image)
        
        if not decoded_objects:
            return jsonify({"error": "No se detectó un código QR en la imagen"}), 400
        
        qr_data = decoded_objects[0].data.decode('utf-8')
        
        parsed_url = urlparse(qr_data)
        if not parsed_url.scheme or not parsed_url.netloc:
            return jsonify({
                "extracted_data": qr_data,
                "is_url": False,
                "message": "Los datos extraídos no son una URL válida"
            })
        
        analysis = check_url_safety(qr_data)
        
        return jsonify({
            "extracted_url": qr_data,
            "is_url": True,
            "analysis": analysis
        })
    
    except Exception as e:
        return jsonify({"error": f"Error al analizar el código QR: {str(e)}"}), 500

@app.route('/health')
def health_check():
    return jsonify({"status": "ok"})

@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html'), 404

@app.errorhandler(500)
def server_error(e):
    return render_template('500.html'), 500

if __name__ == '__main__':
    # Crear la carpeta templates si no existe
    if not os.path.exists('templates'):
        os.makedirs('templates')
    
    # Generar la plantilla principal (index.html)
    with open('templates/index.html', 'w', encoding='utf-8') as f:
        f.write("""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRF</title>
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc08d;
            --warning-color: #e0a800;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --bg-color: #f8f9fa;
            --text-color: #333;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            text-align: center;
            box-shadow: var(--box-shadow);
        }
        .app-title { font-size: 2rem; margin-bottom: 0.5rem; }
        main { flex: 1; padding: 2rem 0; }
        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .card-title { font-size: 1.5rem; color: var(--secondary-color); }
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: var(--accent-color);
            background-color: rgba(79, 192, 141, 0.05);
        }
        /* Iconos locales: se usan marcadores de posición o podrían incrustarse SVGs */
        .upload-icon { font-size: 3rem; margin-bottom: 1rem; }
        .file-input { display: none; }
        .btn {
            display: inline-block;
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover { background-color: #3da775; transform: translateY(-2px); }
        .btn:disabled { background-color: #ccc; cursor: not-allowed; }
        .result-area { display: none; margin-top: 2rem; }
        .result-card {
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .result-safe { background-color: rgba(40, 167, 69, 0.1); border-left: 4px solid var(--success-color); }
        .result-warning { background-color: rgba(224, 168, 0, 0.1); border-left: 4px solid var(--warning-color); }
        .result-danger { background-color: rgba(220, 53, 69, 0.1); border-left: 4px solid var(--danger-color); }
        .result-header { display: flex; align-items: center; margin-bottom: 1rem; }
        .result-icon { font-size: 1.5rem; margin-right: 1rem; }
        .result-title { font-size: 1.2rem; font-weight: 600; }
        .data-item { margin-bottom: 0.75rem; }
        .data-label { font-weight: 600; margin-bottom: 0.25rem; }
        .url-display {
            word-break: break-all;
            padding: 0.5rem;
            background-color: #f5f5f5;
            border-radius: 4px;
            font-family: monospace;
        }
        .content-preview {
            background-color: #f5f5f5;
            padding: 0.75rem;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
            overflow-x: auto;
            max-height: 150px;
            font-size: 0.9rem;
        }
        .loading { display: none; text-align: center; margin: 2rem 0; }
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .error-message {
            color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.1);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: none;
        }
        footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
        }
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .card { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="app-title">Secure Scan & Risk Finder</h1>
            <p>Analiza la seguridad de los códigos QR antes de escanearlos</p>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Sube una imagen con código QR</h2>
                    <p>Analiza la URL contenida en un código QR para verificar su seguridad antes de visitarla</p>
                </div>
                <form id="qr-form" action="/analyze" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
                    <div id="upload-area" class="upload-area">
                        <!-- Se usan iconos locales sin depender de recursos externos -->
                        <i class="upload-icon">Upload</i>
                        <p id="upload-text">Arrastra una imagen aquí o haz clic para seleccionar</p>
                        <p class="small">(Formatos: JPG, PNG, GIF - Max: 5MB)</p>
                        <input type="file" id="file-input" name="qrimage" class="file-input" accept="image/*">
                    </div>
                    <div id="error-message" class="error-message"></div>
                    <div class="text-center">
                        <button type="submit" id="analyze-btn" class="btn" disabled>
                            Analizar QR
                        </button>
                    </div>
                </form>
                <div id="loading" class="loading">
                    <div class="spinner"></div>
                    <p>Analizando código QR...</p>
                </div>
                <div id="result-area" class="result-area">
                    <div id="result-card" class="result-card">
                        <div class="result-header">
                            <span id="result-icon" class="result-icon">Upload</span>
                            <h3 id="result-title" class="result-title"></h3>
                        </div>
                        <div class="data-item">
                            <div class="data-label">URL extraída:</div>
                            <div id="extracted-url" class="url-display"></div>
                        </div>
                        <div id="status-container" class="data-item">
                            <div class="data-label">Estado HTTP:</div>
                            <div id="http-status"></div>
                        </div>
                        <div id="risk-container" class="data-item">
                            <div class="data-label">Nivel de riesgo:</div>
                            <div id="risk-level"></div>
                        </div>
                        <div id="preview-container" class="data-item">
                            <div class="data-label">Vista previa del contenido:</div>
                            <pre id="content-preview" class="content-preview"></pre>
                        </div>
                    </div>
                    <div class="text-center">
                        <button id="scan-again-btn" class="btn">
                            Analizar otro QR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="container">
            <p><a href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> creado por <a href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> está bajo licencia <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer">CC BY-NC-SA 4.0</a></p>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('qr-form');
            const fileInput = document.getElementById('file-input');
            const uploadArea = document.getElementById('upload-area');
            const uploadText = document.getElementById('upload-text');
            const analyzeBtn = document.getElementById('analyze-btn');
            const loadingElement = document.getElementById('loading');
            const resultArea = document.getElementById('result-area');
            const resultCard = document.getElementById('result-card');
            const resultIcon = document.getElementById('result-icon');
            const resultTitle = document.getElementById('result-title');
            const extractedUrl = document.getElementById('extracted-url');
            const httpStatus = document.getElementById('http-status');
            const riskLevel = document.getElementById('risk-level');
            const contentPreview = document.getElementById('content-preview');
            const scanAgainBtn = document.getElementById('scan-again-btn');
            const errorMessage = document.getElementById('error-message');
            const statusContainer = document.getElementById('status-container');
            const riskContainer = document.getElementById('risk-container');
            const previewContainer = document.getElementById('preview-container');
            
            // Arrastrar y soltar
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#4fc08d';
                uploadArea.style.backgroundColor = 'rgba(79, 192, 141, 0.1)';
            });
            uploadArea.addEventListener('dragleave', function() {
                uploadArea.style.borderColor = '#ccc';
                uploadArea.style.backgroundColor = 'transparent';
            });
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#ccc';
                uploadArea.style.backgroundColor = 'transparent';
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    validateFile();
                }
            });
            // Al hacer clic en el área se abre el selector
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            fileInput.addEventListener('change', validateFile);
            
            function validateFile() {
                errorMessage.style.display = 'none';
                if (fileInput.files.length === 0) {
                    analyzeBtn.disabled = true;
                    return;
                }
                const file = fileInput.files[0];
                const fileSize = file.size / 1024 / 1024; // en MB
                const fileType = file.type;
                if (!fileType.startsWith('image/')) {
                    showError('Por favor, selecciona un archivo de imagen válido.');
                    return;
                }
                if (fileSize > 5) {
                    showError('El tamaño del archivo no debe superar los 5MB.');
                    return;
                }
                analyzeBtn.disabled = false;
                uploadText.textContent = file.name;
            }
            
            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                fileInput.value = '';
                analyzeBtn.disabled = true;
                uploadText.textContent = 'Arrastra una imagen aquí o haz clic para seleccionar';
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                resultArea.style.display = 'none';
                loadingElement.style.display = 'block';
                const formData = new FormData(form);
                fetch('/analyze', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    loadingElement.style.display = 'none';
                    if (data.error) {
                        showError(data.error);
                        return;
                    }
                    resultArea.style.display = 'block';
                    extractedUrl.textContent = data.extracted_url || data.extracted_data || 'N/A';
                    if (!data.is_url) {
                        resultCard.className = 'result-card result-warning';
                        resultIcon.textContent = '[!]';
                        resultTitle.textContent = 'No se detectó una URL válida';
                        statusContainer.style.display = 'none';
                        riskContainer.style.display = 'none';
                        previewContainer.style.display = 'none';
                        return;
                    }
                    statusContainer.style.display = 'block';
                    riskContainer.style.display = 'block';
                    previewContainer.style.display = 'block';
                    const analysis = data.analysis;
                    if (analysis.status === 'success') {
                        httpStatus.textContent = analysis.http_status;
                        contentPreview.textContent = analysis.content_preview;
                    } else {
                        httpStatus.textContent = 'N/A';
                        contentPreview.textContent = analysis.message || 'No disponible';
                    }
                    riskLevel.textContent = analysis.risk_level ? analysis.risk_level.charAt(0).toUpperCase() + analysis.risk_level.slice(1) : 'Desconocido';
                    switch(analysis.risk_level) {
                        case 'bajo':
                            resultCard.className = 'result-card result-safe';
                            resultIcon.textContent = '[Seguro]';
                            resultTitle.textContent = 'URL Segura';
                            break;
                        case 'medio':
                            resultCard.className = 'result-card result-warning';
                            resultIcon.textContent = '[!]';
                            resultTitle.textContent = 'URL Potencialmente Insegura';
                            break;
                        case 'alto':
                            resultCard.className = 'result-card result-danger';
                            resultIcon.textContent = '[Peligro]';
                            resultTitle.textContent = 'URL Peligrosa';
                            break;
                        default:
                            resultCard.className = 'result-card result-warning';
                            resultIcon.textContent = '[?]';
                            resultTitle.textContent = 'Seguridad Desconocida';
                    }
                })
                .catch(error => {
                    loadingElement.style.display = 'none';
                    showError('Error al procesar la solicitud. Por favor, intenta de nuevo.');
                    console.error('Error:', error);
                });
            });
            
            scanAgainBtn.addEventListener('click', function() {
                resultArea.style.display = 'none';
                fileInput.value = '';
                analyzeBtn.disabled = true;
                uploadText.textContent = 'Arrastra una imagen aquí o haz clic para seleccionar';
            });
        });
    </script>
</body>
</html>""")
    
    with open('templates/404.html', 'w', encoding='utf-8') as f:
        f.write("""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRF</title>
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc08d;
            --text-color: #333;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            margin: 0;
        }
        .error-container { max-width: 600px; }
        .error-icon { font-size: 5rem; color: var(--primary-color); margin-bottom: 2rem; }
        h1 { font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem; }
        p { font-size: 1.2rem; margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover { background-color: #3da775; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">Upload</div>
        <h1>404 - Página no encontrada</h1>
        <p>La página que estás buscando no existe o ha sido movida a otra ubicación.</p>
        <a href="/" class="btn">Volver a la página principal</a>
    </div>
</body>
</html>""")
    
    with open('templates/500.html', 'w', encoding='utf-8') as f:
        f.write("""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRF</title>
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc08d;
            --danger-color: #dc3545;
            --text-color: #333;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            margin: 0;
        }
        .error-container { max-width: 600px; }
        .error-icon { font-size: 5rem; color: var(--danger-color); margin-bottom: 2rem; }
        h1 { font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem; }
        p { font-size: 1.2rem; margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover { background-color: #3da775; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">Upload</div>
        <h1>500 - Error del servidor</h1>
        <p>Ha ocurrido un error en el servidor. Por favor, inténtalo de nuevo más tarde.</p>
        <a href="/" class="btn">Volver a la página principal</a>
    </div>
</body>
</html>""")

    internal_thread = threading.Thread(target=run_internal_service)
    internal_thread.daemon = False  # Permite que se cierre al terminar la app principal
    internal_thread.start()
    
    print("Inicializando el servicio Secure Scan & Risk Finder...")
    print("Puedes acceder a la aplicación en http://localhost:80")
    app.run(host='0.0.0.0', port=80, debug=False)
