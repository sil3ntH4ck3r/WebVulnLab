from flask import Flask, render_template, url_for, flash, redirect, request, session, jsonify, make_response, send_file, Response
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import create_engine
from sqlalchemy.exc import OperationalError
from io import BytesIO
from reportlab.platypus import SimpleDocTemplate
import time
import jwt
import random
import string
import requests
import os
import base64
from http import HTTPStatus
from reportlab.lib.pagesizes import letter
from PIL import Image as PILImage
from reportlab.platypus import Image

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://username:password@printing_db:5432/printing_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Define the Gallery authorization endpoint and redirect URI
GALLERY_AUTHORIZATION_ENDPOINT = "http://oauth_gallery.local/auth/callback"
GALLERY_REDIRECT_URI = "http://oauth_printing.local/gallery/photos"

db = SQLAlchemy(app)

class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password = db.Column(db.String(60), nullable=False)
    gallery_linked = db.Column(db.Boolean, default=False)
    client_id = db.Column(db.String(15), unique=True, nullable=False)

class ImageData:
    def __init__(self, filename, data):
        self.filename = filename
        self.data = data

def try_connect_to_db(retry_limit=3, delay=1):
    for attempt in range(retry_limit):
        try:
            engine = create_engine(app.config['SQLALCHEMY_DATABASE_URI'])
            engine.connect()
            return True
        except OperationalError:
            print(f"Connection attempt {attempt + 1} failed. Retrying in {delay} seconds...")
            time.sleep(delay)
    print(f"Connection to the database failed after {retry_limit} attempts.")
    return False
    
def generate_client_id():
    return ''.join(random.choices(string.ascii_letters + string.digits, k=15))

def obtener_client_id():
    # Obtener el token JWT de la cookie jwt_printing
    token = request.cookies.get('jwt_printing')
    
    # Decodificar el token JWT para obtener el payload
    try:
        payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
        # Extraer el client_id del payload
        client_id = payload.get('client_id')
        return client_id
    except jwt.ExpiredSignatureError:
        # Manejar el caso en que el token JWT haya expirado
        return None
    except jwt.InvalidTokenError:
        # Manejar el caso en que el token JWT sea inválido
        return None

# Definir una función para verificar si el usuario está autenticado
def is_authenticated():
    return 'jwt_printing' in request.cookies

@app.route("/oauth/code", methods=['GET'])
def oauth_code():
    # Obtener el código de la URL
    code = request.args.get('codigo')
    redirect_uri = request.args.get('redirect_uri')
    cookie = request.cookies.get('jwt_printing')

    # Check if the code was provided
    if code is None:
        return 'The code was not provided', 400

    if redirect_uri is None:
        return 'The redirect_uri was not provided', 400

    # Define the URL for the redirect
    redirect_url = 'http://oauth_gallery.local/oauth/authorize/token'

    # Obtener el client_id
    client_id = obtener_client_id()

    try:
        # Crear un formulario HTML con el código, redirect_uri y client_id como campos ocultos
        form_html = """
        <html>
        <body onload="document.forms[0].submit()">
        <form method="post" action="{url}">
            <input type="hidden" name="code" value="{code}">
            <input type="hidden" name="redirect_uri" value="{redirect_uri}">
            <input type="hidden" name="client_id" value="{client_id}">
            <input type="hidden" name="cookie" value="{cookie}">
            <!-- Agrega cualquier otro campo necesario aquí -->
        </form>
        </body>
        </html>
        """.format(url=redirect_url, code=code, redirect_uri=redirect_uri, client_id=client_id, cookie=cookie)

        # Redirigir con el formulario HTML como respuesta
        return form_html, 200
    except Exception as e:
        # Manejar excepciones
        return 'Error: {}'.format(e), 500

@app.route("/connect-gallery")
def connect_gallery():
    if 'jwt_printing' in request.cookies:  # Verificar si el usuario está autenticado
        token = request.cookies.get('jwt_printing')
        try:
            payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
            email = payload['email']
            user = User.query.filter_by(email=email).first()
            if user:
                jwt_cookie = request.cookies.get('jwt_printing')

                try:
                    decoded_token = jwt.decode(jwt_cookie, app.config['SECRET_KEY'], algorithms=['HS256'])
                    token_client_id = decoded_token.get('client_id')
                    if not token_client_id or token_client_id != user.client_id:
                        return 'Error: client_id inválido', 400
                except jwt.ExpiredSignatureError:
                    return 'Error: token JWT expirado', 400
                except jwt.InvalidTokenError:
                    return 'Error: token JWT inválido', 400
                return redirect(f'{GALLERY_AUTHORIZATION_ENDPOINT}?response_type=code&client_id={user.client_id}&redirect_uri={GALLERY_REDIRECT_URI}&scope=photos')
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')

    # Si el usuario no está autenticado o no se pudo obtener el client_id, redirigir al usuario al inicio de sesión
    return redirect(url_for('login'))

@app.route("/")
def home():
    return render_template('home.html', user_is_authenticated=is_authenticated())

@app.route("/register", methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username']
        email = request.form['email']
        password = request.form['password']

        # Verificar si el nombre de usuario ya está en uso
        existing_username = User.query.filter_by(username=username).first()
        if existing_username:
            flash('Username already exists. Please choose a different one.', 'danger')
            return redirect(request.url)
        
        # Verificar si el correo electrónico ya está en uso
        existing_email = User.query.filter_by(email=email).first()
        if existing_email:
            flash('Email already exists. Please use a different one or login.', 'danger')
            return redirect(request.url)

        # Generar un client_id aleatorio
        client_id = generate_client_id()

        # Crear un nuevo usuario con el client_id generado
        user = User(username=username, email=email, password=password, client_id=client_id)
        
        db.session.add(user)
        db.session.commit()
        flash('Account created!', 'success')
        return redirect(url_for('profile'))
    return render_template('register.html')

@app.route("/login", methods=['GET', 'POST'])
def login():
    # Verificar si el usuario está autenticado usando la función is_authenticated
    if is_authenticated():
        return redirect(url_for('profile'))

    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        user = User.query.filter_by(email=email).first()
        if user and user.password == password:
            # Crear el token JWT con la información del usuario
            token = jwt.encode({'email': user.email, 'client_id': user.client_id}, app.config['SECRET_KEY'], algorithm='HS256')

            # Crear una respuesta
            response = make_response(redirect(url_for('profile')))
            # Establecer la cookie con el token JWT
            response.set_cookie('jwt_printing', token)

            return response
        else:
            flash('Login failed. Please check your email and password.', 'danger')
    return render_template('login.html')

@app.route("/profile", methods=['GET', 'POST'])
def profile():

    if request.method == 'POST':
        action = request.form.get('action')
        if action == 'unlink_gallery':
            # Eliminar la cookie oauth_token
            response = redirect(url_for('profile'))
            response.delete_cookie('oauth_token')
            return response
    
    if request.method == 'POST':
        action = request.form.get('action')
        if action == 'logout':
            # Eliminar la cookie oauth_token
            response = redirect(url_for('profile'))
            response.delete_cookie('jwt_printing')
            return response

    # Obtener el token JWT de la cookie 'jwt_printing' y 'oauth_token'
    jwt_printing_token = request.cookies.get('jwt_printing')
    oauth_token = request.cookies.get('oauth_token')

    # Verificar si se proporciona 'jwt_printing_token'
    if jwt_printing_token:
        try:
            # Verificar y decodificar el token JWT 'jwt_printing_token'
            payload = jwt.decode(jwt_printing_token, app.config['SECRET_KEY'], algorithms=['HS256'])
            email = payload['email']
            user = User.query.filter_by(email=email).first()
            if user:
                # Si el token es válido y el usuario existe
                # Verificar si se proporciona 'oauth_token'
                if oauth_token:
                    try:
                        # Verificar y decodificar el token JWT 'oauth_token'
                        payload = jwt.decode(oauth_token, app.config['SECRET_KEY'], algorithms=['HS256'])
                        if payload:
                            # Si el token es válido y el usuario existe, mostrar la página de perfil con gallery_linked=True
                            return render_template('profile.html', user=user, gallery_linked=True)
                    except jwt.ExpiredSignatureError:
                        pass
                    except jwt.InvalidTokenError:
                        pass
                
                # Si no se proporciona 'oauth_token' o es inválido, mostrar la página de perfil con gallery_linked=False
                return render_template('profile.html', user=user, gallery_linked=False)
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')

    # Si el token 'jwt_printing_token' es inválido o no se proporciona, redirigir al usuario al inicio de sesión
    return redirect(url_for('login'))

@app.route("/gallery/photos", methods=['GET'])
def gallery_photos():
    # Obtener el valor del parámetro oauth_token de la URL
    oauth_token = request.args.get('oauth_token')

    # Verificar si se proporciona un oauth_token en la URL
    if oauth_token:
        try:
            # Decodificar el oauth_token
            decode = jwt.decode(oauth_token, app.config['SECRET_KEY'], algorithms=['HS256'])
        except jwt.ExpiredSignatureError:
            # Manejar oauth_token caducado
            return 'Error: oauth_token expirado', 400
        except jwt.InvalidTokenError:
            # Manejar oauth_token inválido
            return 'Error: oauth_token inválido', 400

        # Verificar si el token es válido
        if decode:
            # Verificar si los parámetros codigo y client_id están presentes en el token decodificado
            if 'codigo' in decode and 'client_id' in decode:
                # Si el token es válido y contiene los parámetros requeridos, establecerlo como cookie y redirigir al usuario
                response = make_response(redirect('http://oauth_printing.local/gallery/photos'))
                response.set_cookie('oauth_token', oauth_token)
                return response
            else:
                # Manejar el caso donde el token no contiene los parámetros requeridos
                return 'Error: oauth_token inválido', 400
        else:
            # Manejar el caso donde el token no es válido
            return 'Error: oauth_token no válido', 400
    
    # Obtener el valor de la cookie llamada oauth_token
    oauth_token = request.cookies.get('oauth_token')
    
    # Verificar si se encontró el token
    if not oauth_token:
        # Si no se encontró el token en la cookie, mostrar un mensaje de error
        flash('You must link your gallery account', 'danger')
        return redirect(url_for('login'))  # Redirigir al usuario al inicio de sesión
    
    try:
        # Enviar solicitud GET a localhost:5001/get_images con el encabezado de autorización adecuado
        response = requests.get('http://oauth.gallery.local:5001/get_images', headers={'Authorization': f'Bearer {oauth_token}'})
        
        # Verificar si la solicitud fue exitosa (código de estado 200)
        if response.status_code == 200:
            # Verificar si hay datos en la respuesta
            images_data = response.json()
            if not images_data:
                # Si no hay imágenes en la respuesta, mostrar un mensaje de error
                flash('No images found in the gallery yet. Upload images on Gallery service', 'danger')
                return render_template('gallery.html', images=[])  # Renderizar la plantilla con una lista vacía de imágenes
            else:
                # Renderizar la plantilla y pasar las imágenes como contexto
                return render_template('gallery.html', images=images_data)
        else:
            # Si la solicitud no fue exitosa, mostrar un mensaje de error
            flash('Failed to retrieve images from the server.', 'danger')
    except Exception as e:
        # Si ocurre alguna excepción, mostrar un mensaje de error
        flash(f'Error: {str(e)}', 'danger')
    
    return redirect(url_for('login'))  # Redirigir al usuario al inicio de sesión

@app.route('/generate_document', methods=['POST'])
def generate_document():
    selected_images = request.form.getlist('selected_images[]')
    image_data = request.form.getlist('image_data[]')

    image_objects = []
    for filename, img_data in zip(selected_images, image_data):
        img_bytes = base64.b64decode(img_data)
        image_objects.append(ImageData(filename, img_bytes))

    # Filtrar las imágenes seleccionadas
    selected_image_objects = [img_obj for img_obj in image_objects if img_obj.filename in selected_images]

    # Crear un objeto BytesIO para almacenar el PDF generado
    buffer = BytesIO()

    # Crear un documento PDF
    doc = SimpleDocTemplate(buffer, pagesize=letter)
    elements = []

    # Agregar las imágenes seleccionadas al documento PDF
    for img_obj in selected_image_objects:
        img = PILImage.open(BytesIO(img_obj.data))
        img = img.resize((400, 400))  # Ajustar el tamaño de la imagen si es necesario
        img_bytes = BytesIO()
        img.save(img_bytes, format='JPEG')
        img_bytes.seek(0)
        img_obj_pdf = Image(img_bytes)
        elements.append(img_obj_pdf)

    doc.build(elements)

    # Devolver el PDF generado como una respuesta directa
    buffer.seek(0)
    return Response(buffer, mimetype='application/pdf')

if __name__ == '__main__':
    if try_connect_to_db():
        with app.app_context():
            db.create_all()
        app.run(debug=True, host='0.0.0.0', port=5000)