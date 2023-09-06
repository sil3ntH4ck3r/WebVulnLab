import os
import json
import base64
import yaml
from flask import Flask, render_template, request, jsonify

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
        serialized_data = f"yaml: {article_data['content']}"
        return render_template('post.html', serialized_data=serialized_data)
    else:
        return "Artículo no encontrado", 404

@app.route('/deserialize', methods=['POST'])
def deserialize():
    try:
        input_data = request.form.get('input')
        yaml_bytes = base64.b64decode(input_data)
        content = yaml.load(yaml_bytes, Loader=yaml.Loader)
        return content['yaml']
    except Exception as e:
        return str(e), 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)