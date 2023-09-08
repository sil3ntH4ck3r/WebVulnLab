from flask import Flask, request, render_template, jsonify
import os
import json
import pickle

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/post/<int:post_id>')
def post(post_id):
    article_file = os.path.join('articles', f'{post_id}.json')
    if os.path.exists(article_file):
        with open(article_file, 'r') as f:
            article_data = json.load(f)
        content = article_data.get('content', '')  # Obtener el valor 'content' del JSON
        serialized_data = pickle.dumps(content)  # Serializar el contenido
        hex_data = serialized_data.hex()  # Codificar en hexadecimal
        return render_template('post.html', serialized_data=hex_data)
    else:
        return "Artículo no encontrado", 404

@app.route('/deserialize', methods=['POST'])
def deserialize():
    try:
        hex_data = request.form['input']
        serialized_data = bytes.fromhex(hex_data)  # Decodificar de hexadecimal
        data = pickle.loads(serialized_data)
        return str(data)
    except Exception as e:
        return str(e), 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
