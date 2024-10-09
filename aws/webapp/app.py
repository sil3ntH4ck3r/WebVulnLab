from flask import Flask, request, redirect, url_for, render_template, send_file
import boto3
import os
import uuid
import json

app = Flask(__name__)

# Configurar Boto3 para usar LocalStack
s3 = boto3.client('s3', endpoint_url='http://localstack:4566')
lambda_client = boto3.client('lambda', endpoint_url='http://localstack:4566')

BUCKET_NAME = 'images-bucket'
PROCESSED_BUCKET_NAME = 'processed-images'

def create_buckets():
    try:
        s3.create_bucket(Bucket=BUCKET_NAME)
    except s3.exceptions.BucketAlreadyOwnedByYou:
        pass
    try:
        s3.create_bucket(Bucket=PROCESSED_BUCKET_NAME)
    except s3.exceptions.BucketAlreadyOwnedByYou:
        pass

def create_lambda_functions():
    # Rotar imagen
    create_lambda_function('rotate_image', rotate_image_code)
    # Escala de grises
    create_lambda_function('grayscale_image', grayscale_image_code)
    # Redimensionar imagen
    create_lambda_function('resize_image', resize_image_code)

def create_lambda_function(function_name, code):
    zip_file = create_lambda_zip(code)
    try:
        lambda_client.create_function(
            FunctionName=function_name,
            Runtime='python3.8',
            Role='arn:aws:iam::000000000000:role/lambda-role',
            Handler='lambda_function.lambda_handler',
            Code={'ZipFile': zip_file},
            Publish=True
        )
    except lambda_client.exceptions.ResourceConflictException:
        pass  # La función ya existe

def create_lambda_zip(code):
    import zipfile
    import io
    import tempfile
    import subprocess
    import sys
    import shutil

    # Crear un directorio temporal
    with tempfile.TemporaryDirectory() as tempdir:
        # Escribir el archivo lambda_function.py
        lambda_path = os.path.join(tempdir, 'lambda_function.py')
        with open(lambda_path, 'w') as f:
            f.write(code)
        
        # Instalar Pillow en el directorio temporal
        subprocess.check_call([
            sys.executable, '-m', 'pip', 'install', 'Pillow', '-t', tempdir
        ])
        
        # Crear el archivo ZIP
        zip_buffer = io.BytesIO()
        with zipfile.ZipFile(zip_buffer, 'w', zipfile.ZIP_DEFLATED) as zf:
            # Agregar todos los archivos del directorio temporal al ZIP
            for root, dirs, files in os.walk(tempdir):
                for file in files:
                    filepath = os.path.join(root, file)
                    arcname = os.path.relpath(filepath, start=tempdir)
                    zf.write(filepath, arcname)
        return zip_buffer.getvalue()


# Código para las funciones Lambda
rotate_image_code = '''
import boto3
from PIL import Image
import os
import base64
import io
import json

def lambda_handler(event, context):
    localstack_host = os.environ.get('LOCALSTACK_HOSTNAME', 'localhost')
    s3_endpoint = f'http://{localstack_host}:4566'  # Usar simples llaves
    s3 = boto3.client('s3', endpoint_url=s3_endpoint)
    image_id = event['image_id']
    angle = event.get('angle', 90)

    # Descargar imagen
    s3.download_file('images-bucket', image_id, '/tmp/input.png')

    # Rotar imagen
    image = Image.open('/tmp/input.png')
    rotated = image.rotate(angle)

    # Guardar la imagen en un buffer
    buffer = io.BytesIO()
    rotated.save(buffer, format='PNG')
    buffer.seek(0)

    # Codificar la imagen en base64
    image_base64 = base64.b64encode(buffer.read()).decode('utf-8')

    # Devolver la imagen codificada en base64
    return {
        'statusCode': 200,
        'headers': {'Content-Type': 'application/json'},
        'body': json.dumps({'image_base64': image_base64})
    }
'''


grayscale_image_code = '''
import boto3
from PIL import Image, ImageOps
import os
import base64
import io
import json

def lambda_handler(event, context):
    localstack_host = os.environ.get('LOCALSTACK_HOSTNAME', 'localhost')
    s3_endpoint = f'http://{localstack_host}:4566'  # Usar simples llaves
    s3 = boto3.client('s3', endpoint_url=s3_endpoint)
    image_id = event['image_id']

    # Descargar imagen
    s3.download_file('images-bucket', image_id, '/tmp/input.png')

    # Convertir a escala de grises
    image = Image.open('/tmp/input.png')
    grayscale = ImageOps.grayscale(image)

    # Guardar la imagen en un buffer
    buffer = io.BytesIO()
    grayscale.save(buffer, format='PNG')
    buffer.seek(0)

    # Codificar la imagen en base64
    image_base64 = base64.b64encode(buffer.read()).decode('utf-8')

    # Devolver la imagen codificada en base64
    return {
        'statusCode': 200,
        'headers': {'Content-Type': 'application/json'},
        'body': json.dumps({'image_base64': image_base64})
    }
'''



resize_image_code = '''
import boto3
from PIL import Image
import os
import base64
import io
import json

def lambda_handler(event, context):
    localstack_host = os.environ.get('LOCALSTACK_HOSTNAME', 'localhost')
    s3_endpoint = f'http://{localstack_host}:4566'  # Usar simples llaves
    s3 = boto3.client('s3', endpoint_url=s3_endpoint)
    image_id = event['image_id']
    width = event.get('width', 200)
    height = event.get('height', 200)

    # Descargar imagen
    s3.download_file('images-bucket', image_id, '/tmp/input.png')

    # Redimensionar imagen
    image = Image.open('/tmp/input.png')
    resized = image.resize((width, height))

    # Guardar la imagen en un buffer
    buffer = io.BytesIO()
    resized.save(buffer, format='PNG')
    buffer.seek(0)

    # Codificar la imagen en base64
    image_base64 = base64.b64encode(buffer.read()).decode('utf-8')

    # Devolver la imagen codificada en base64
    return {
        'statusCode': 200,
        'headers': {'Content-Type': 'application/json'},
        'body': json.dumps({'image_base64': image_base64})
    }
'''



@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        # Subir imagen a S3
        file = request.files['image']
        image_id = str(uuid.uuid4()) + os.path.splitext(file.filename)[1]
        s3.upload_fileobj(file, BUCKET_NAME, image_id)
        return redirect(url_for('edit', image_id=image_id))
    return render_template('index.html')

import base64

@app.route('/edit/<image_id>', methods=['GET', 'POST'])
def edit(image_id):
    if request.method == 'POST':
        # Obtener el nombre de la acción desde el formulario
        action = request.form['action']
        function_name = action  # Variable que el usuario puede manipular

        # Obtener parámetros adicionales si es necesario
        additional_params = {}
        if 'angle' in request.form:
            additional_params['angle'] = int(request.form['angle'])
        if 'width' in request.form and 'height' in request.form:
            additional_params['width'] = int(request.form['width'])
            additional_params['height'] = int(request.form['height'])

        # Invocar la función Lambda y obtener la respuesta
        response_payload = invoke_lambda_function(function_name, image_id, additional_params)

        # Decodificar la imagen base64
        if 'image_base64' in response_payload:
            image_data = base64.b64decode(response_payload['image_base64'])
            # Guardar la imagen en /tmp para servirla
            image_path = f'/tmp/{image_id}'
            with open(image_path, 'wb') as f:
                f.write(image_data)
            # Mostrar la imagen al usuario
            return send_file(image_path, mimetype='image/png')
        else:
            return f"Error: {response_payload}"

    return render_template('edit.html', image_id=image_id)

def invoke_lambda_function(function_name, image_id, additional_params=None):
    payload = {'image_id': image_id}
    if additional_params:
        payload.update(additional_params)
    response = lambda_client.invoke(
        FunctionName=function_name,
        InvocationType='RequestResponse',
        Payload=json.dumps(payload)
    )
    response_payload = json.loads(response['Payload'].read().decode('utf-8'))
    return response_payload


@app.route('/result/<image_id>')
def result(image_id):
    # Descargar la imagen procesada de S3
    file_path = f'/tmp/{image_id}'
    try:
        s3.download_file(PROCESSED_BUCKET_NAME, image_id, file_path)
        # Pasar la ruta de la imagen a la plantilla
        return render_template('result.html', image_filename=image_id)
    except Exception as e:
        print(e)
        return "El procesamiento aún no está completo. Por favor, actualiza la página después de unos momentos."

@app.route('/download/<image_id>')
def download_image(image_id):
    file_path = f'/tmp/{image_id}'
    return send_file(file_path)

if __name__ == '__main__':
    create_buckets()
    create_lambda_functions()
    app.run(host='0.0.0.0', port=5000, debug=True)