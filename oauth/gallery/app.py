from flask import Flask, render_template, url_for, flash, redirect, request, session, jsonify, make_response, send_from_directory, send_file, current_app
from flask_sqlalchemy import SQLAlchemy
from flask_login import UserMixin
from sqlalchemy import create_engine, event
from datetime import datetime
from sqlalchemy.exc import OperationalError
from werkzeug.utils import secure_filename
import base64
import time
import jwt
import random
import string
import io
import os
from reportlab.lib.pagesizes import letter
from PIL import Image as PILImage
from reportlab.platypus import Image

app = Flask(__name__)
UPLOAD_FOLDER = 'uploads'  # Carpeta donde se guardarán los archivos
app.config['SECRET_KEY'] = 'your_secret_key'
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://username:password@gallery_db:5432/gallery_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

# Define the Gallery authorization endpoint and redirect URI
GALLERY_AUTHORIZATION_ENDPOINT = "http://oauth_gallery.local/auth/callback"
GALLERY_REDIRECT_URI = "http://oauth_printing.local/gallery/photos"

db = SQLAlchemy(app)

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password = db.Column(db.String(60), nullable=False)
    images = db.relationship('Image', backref='user', lazy=True)
    codigo = db.Column(db.String(4), nullable=True)

class Image(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    filename = db.Column(db.String(100), nullable=False)
    data = db.Column(db.LargeBinary, nullable=False)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    uploaded_at = db.Column(db.DateTime, default=datetime.utcnow)

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

def generate_random_code():
    return ''.join(random.choices(string.digits, k=4))

def create_default_user():
    default_username = 'admin'
    default_email = 'admin@gallery.local'
    default_password = 'Admin3$!'

    # Check if the default user already exists
    existing_user = User.query.filter_by(username=default_username).first()
    if existing_user:
        return

    # Create the default user
    default_user = User(username=default_username, email=default_email, password=default_password)
    
    # Generate a random code and assign it to the user
    default_user.codigo = generate_random_code()
    
    db.session.add(default_user)
    db.session.commit()

    # Create the default image associated with the default user
    image_path = 'testing.png'  # Path to the image
    # Después de leer el archivo testing.png
    with open(image_path, 'rb') as img_file:
        image_data = img_file.read()
        filename = 'testing.jpg'  # Cambiar la extensión del archivo a .jpg

        # Convertir la imagen a formato JPEG
        img = PILImage.open(io.BytesIO(image_data))
        buffer = io.BytesIO()
        # Convertir la imagen a modo RGB antes de guardarla como JPEG
        img = img.convert('RGB')
        img.save(buffer, format='JPEG')
        image_data_jpg = buffer.getvalue()

        new_image = Image(filename=filename, data=image_data_jpg, user_id=default_user.id)
        db.session.add(new_image)
        db.session.commit()

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
    return 'jwt_gallery' in request.cookies

def obtener_user_id_de_jwt(request):
    # Obtener el token JWT de la solicitud
    token = request.cookies.get('jwt_gallery')
    if token:
        try:
            # Decodificar el token JWT y obtener el correo electrónico
            payload = jwt.decode(token, current_app.config['SECRET_KEY'], algorithms=['HS256'])
            email = payload.get('email')
            if email:
                # Buscar el usuario en la base de datos por correo electrónico
                user = User.query.filter_by(email=email).first()
                if user:
                    # Devolver el ID del usuario encontrado
                    return user.id
        except jwt.ExpiredSignatureError:
            pass  # El token ha caducado
        except jwt.InvalidTokenError:
            pass  # El token es inválido

    # Si no se puede obtener el ID del usuario, devolver None o lanzar una excepción según tus necesidades
    return None

# Función para eliminar un archivo
def delete_file(file_name):
    user_id = obtener_user_id_de_jwt(request)
    if user_id:
        upload_folder = os.path.join(app.config['UPLOAD_FOLDER'], str(user_id))
        file_path = os.path.join(upload_folder, file_name)
        if os.path.exists(file_path):
            os.remove(file_path)
            return True
    return False

@app.route('/auth/callback', methods=['GET'])
def oauth_callback():
    # Obtener los parámetros de la URL
    response_type = request.args.get('response_type')
    client_id = request.args.get('client_id')
    redirect_uri = request.args.get('redirect_uri')
    scope = request.args.get('scope')

    # Verificar que los datos sean correctos
    if response_type != 'code':
        return 'Error: response_type incorrecto', 400

    # Verificar el client_id con la cookie jwt_gallery
    jwt_cookie = request.cookies.get('jwt_gallery')
    if not jwt_cookie:
        # Si la cookie no se encuentra, redirigir al usuario a la página de inicio de sesión
        login_url = url_for('login', next='/auth/callback')
        return redirect(login_url)

    jwt_cookie = request.cookies.get('jwt_printing')

    # Verificar el redirect_uri si es necesario
    if redirect_uri is None:
        return 'The redirect_uri was not provided', 400
    # Verificar el scope si es necesario
    if scope != 'photos':
        return 'Error: scope incorrecto', 400

    # Generar un código de 4 dígitos
    codigo = ''.join(random.choices('0123456789', k=4))

    jwt_cookie = request.cookies.get('jwt_gallery')
    decoded_token = jwt.decode(jwt_cookie, app.config['SECRET_KEY'], algorithms=['HS256'])

    # Obtener el usuario actual
    user_email = decoded_token.get('email')
    user = User.query.filter_by(email=user_email).first()
    if user:
        # Asignar el código al usuario
        user.codigo = codigo
        db.session.commit()
    else:
        return 'Error: Usuario no encontrado', 400

    # Mostrar un panel solicitando la autorización del usuario
    return render_template('autorizacion.html', redirect_uri=redirect_uri, codigo=codigo)

@app.route('/oauth/authorize', methods=['POST'])
def procesar_autorizacion():
    decision = request.form.get('decision')
    redirect_uri = request.form.get('redirect_uri')

    # Comprobar si se proporcionan los parámetros código y redirect_uri
    codigo = request.form.get('codigo')
    if not codigo or not redirect_uri:
        return 'Faltan parámetros requeridos', 400
    
    # Comprobar si el código es válido
    user = User.query.filter_by(codigo=codigo).first()
    if not user:
        return 'Código inválido', 400

    if decision == 'si':
        # Si el usuario acepta, redirigirlo a la URL proporcionada en redirect_uri
        redirect_uri_con_codigo = f"http://oauth_printing.local/oauth/code?codigo={codigo}&redirect_uri={redirect_uri}"
        return redirect(redirect_uri_con_codigo)
    else:
        # Si el usuario rechaza, redirigirlo a http://oauth_printing.local/profile
        return redirect("http://oauth_printing.local/profile")

@app.route("/")
def home():
    return render_template('home.html', user_is_authenticated=is_authenticated())

@app.route('/oauth/authorize/token', methods=['POST'])
def procesar_autorizacion_token():
    # Obtener los parámetros de la solicitud POST
    codigo = request.form.get('code')
    redirect_uri = request.form.get('redirect_uri')
    client_id = request.form.get('client_id')
    token = request.form.get('cookie')

    # Verificar si se proporcionaron todos los parámetros necesarios
    if not (codigo and redirect_uri and client_id):
        return 'Bad Request: Falta alguno de los parámetros', 400

    # Verificar si el código es válido consultando la base de datos
    user = User.query.filter_by(codigo=codigo).first()
    if not user:
        return 'Unauthorized: Código inválido', 401

    if not token:
        return 'Unauthorized: Falta la cookie jwt_printing', 401

    try:
        payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
        cookie_client_id = payload.get('client_id')
        if cookie_client_id != client_id:
            return 'Unauthorized: client_id de la cookie no coincide', 401
    except jwt.ExpiredSignatureError:
        return 'Unauthorized: Token JWT expirado', 401
    except jwt.InvalidTokenError:
        return 'Unauthorized: Token JWT inválido', 401

    # Generar un token de autorización Bearer sin tiempo de expiración
    token_payload = {
        'client_id': client_id,
        'codigo': codigo,
    }
    token = jwt.encode(token_payload, app.config['SECRET_KEY'], algorithm='HS256')

     # Añadir el token a la URL de redirección como parámetro
    redirect_uri_with_token = f"{redirect_uri}?oauth_token={token}"

    # Crear una respuesta de redirección con la URL que incluye el token
    response = redirect(redirect_uri_with_token)

    return response
    

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

        # Si no hay coincidencias, crear un nuevo usuario
        new_user = User(username=username, email=email, password=password)
        db.session.add(new_user)
        db.session.commit()
        
        # Verificar si hay una URL de redirección almacenada en la sesión
        if 'next_url' in session:
            next_url = session['next_url']
            del session['next_url']  # Eliminar la URL de redirección de la sesión
            return redirect(next_url)
        
        return redirect(url_for('login'))
    
    # Guardar la URL de referencia en la sesión
    if request.referrer:
        session['next_url'] = request.referrer
        
    return render_template('register.html')

@app.route("/login", methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        user = User.query.filter_by(email=email).first()
        if user and user.password == password:
            # Crear el token JWT con la información del usuario
            token = jwt.encode({'email': user.email}, app.config['SECRET_KEY'], algorithm='HS256')

            # Crear una respuesta
            response = make_response(redirect(url_for('profile')))
            # Establecer la cookie con el token JWT
            response.set_cookie('jwt_gallery', token)

            # Verificar si hay una URL de redireccionamiento después del inicio de sesión
            next_url = request.args.get('next')
            if next_url and next_url == url_for('oauth_callback'):
                # Construir la URL de redirección con los parámetros requeridos
                redirect_url = (
                    "http://oauth_gallery.local/auth/callback"
                    "?response_type=code"
                    f"&client_id={obtener_client_id()}"  # Obtener el client_id de alguna manera
                    f"&redirect_uri={GALLERY_REDIRECT_URI}"
                    "&scope=photos"
                )
                
                # Crear una respuesta con la redirección a la URL construida
                response = make_response(redirect(redirect_url))
                # Establecer la cookie con el token JWT
                response.set_cookie('jwt_gallery', token)

            return response
        else:
            flash('Login failed. Please check your email and password.', 'danger')
    return render_template('login.html')

@app.route("/profile", methods=['GET', 'POST'])
def profile():
    # Obtener el token JWT de la cookie
    token = request.cookies.get('jwt_gallery')
    if token:
        try:
            # Verificar y decodificar el token JWT
            payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
            email = payload['email']
            user = User.query.filter_by(email=email).first()
            if user:
                # Si el token es válido y el usuario existe, mostrar la página de perfil
                return render_template('profile.html', user=user)
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')
    # Si el token es inválido o no se proporciona, redirigir al usuario al inicio de sesión
    return redirect(url_for('login'))

def allowed_file(filename):
    # This function is no longer needed when uploading images to the database
    return True

@app.route('/upload', methods=['GET', 'POST'])
def upload():
    # Verificar si el método de solicitud es POST
    if request.method == 'POST':
        # Obtener el token JWT de la cookie
        token = request.cookies.get('jwt_gallery')
        if token:
            try:
                # Verificar y decodificar el token JWT
                payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
                email = payload['email']
                user = User.query.filter_by(email=email).first()
                if user:
                    # Si el token es válido y el usuario existe, permitir la carga de archivos
                    # Verificar si se ha enviado un archivo
                    if 'file' not in request.files:
                        flash('No file part', 'danger')
                        return redirect(request.url)
                    
                    file = request.files['file']
                    
                    # Verificar si el archivo tiene un nombre válido
                    if file.filename == '':
                        flash('No selected file', 'danger')
                        return redirect(request.url)
                    
                    # Verificar si el archivo es una imagen
                    if file.mimetype.startswith('image'):
                        filename = secure_filename(file.filename)
                        image_data = file.read()
                        # Imprimir image_data en la salida del servidor
                        print("Image Data:", image_data)
                        new_image = Image(filename=filename, data=image_data, user_id=user.id)
                        db.session.add(new_image)
                        db.session.commit()
                        flash('File uploaded successfully', 'success')
                        return redirect(request.url)
                    else:
                        # No es una imagen, guardar el archivo
                        filename = secure_filename(file.filename)
                        filepath = os.path.join(app.config['UPLOAD_FOLDER'], str(user.id), filename)
                        os.makedirs(os.path.dirname(filepath), exist_ok=True)
                        file.save(filepath)
                        flash('File uploaded successfully', 'success')
                    return redirect(url_for('cloud'))
            except jwt.ExpiredSignatureError:
                flash('Token expired. Please log in again.', 'danger')
            except jwt.InvalidTokenError:
                flash('Invalid token. Please log in again.', 'danger')
        else:
            flash('Unauthorized access. Please log in.', 'danger')
            return redirect(url_for('login'))

    if request.method == 'GET':
        token = request.cookies.get('jwt_gallery')
        if token:
            try:
                # Verificar y decodificar el token JWT
                payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
                email = payload['email']
                if email:
                    return render_template('upload.html')
            except jwt.ExpiredSignatureError:
                flash('Token expired. Please log in again.', 'danger')
                return render_template('login.html')
            except jwt.InvalidTokenError:
                flash('Invalid token. Please log in again.', 'danger')
                return render_template('login.html')
        else:
            flash('Unauthorized access. Please log in.', 'danger')
            return render_template('login.html')

@app.route('/gallery')
def gallery():
    # Obtener el token JWT de la cookie
    token = request.cookies.get('jwt_gallery')
    if token:
        try:
            # Verificar y decodificar el token JWT
            payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
            email = payload['email']
            user = User.query.filter_by(email=email).first()
            if user:
                # Si el token es válido y el usuario existe, mostrar las imágenes del usuario
                images = [{'filename': img.filename, 'data': base64.b64encode(img.data).decode('ascii'), 'uploaded_at': img.uploaded_at} for img in user.images]
                return render_template('gallery.html', images=images)
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')

    # Si el token es inválido o no se proporciona, redirigir al usuario al inicio de sesión
    flash('Unauthorized access. Please log in.', 'danger')
    return redirect(url_for('login'))

@app.route('/get_images')
def get_images():
    # Obtener el encabezado de autorización de la solicitud
    authorization_header = request.headers.get('Authorization')

    # Verificar si se proporcionó un encabezado de autorización
    if authorization_header:
        # Separar el encabezado de autorización en tipo y token
        auth_parts = authorization_header.split()
        if len(auth_parts) == 2 and auth_parts[0] == 'Bearer':
            token = auth_parts[1]

            try:
                # Verificar y decodificar el token JWT
                payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
                
                # Verificar si el payload del token contiene el parámetro 'codigo'
                if 'codigo' in payload:
                    codigo = payload['codigo']
                    
                    # Buscar al usuario asociado al código
                    user = User.query.filter_by(codigo=codigo).first()
                    if user:
                        # Si se encuentra al usuario, devolver las imágenes asociadas a ese usuario
                        images = [{'filename': img.filename, 'data': base64.b64encode(img.data).decode('ascii')} for img in user.images]
                        return jsonify(images)
                    else:
                        return jsonify({'error': 'User not found for the given code'}), 404
                else:
                    return jsonify({'error': 'Codigo parameter missing in token payload'}), 400
            except jwt.ExpiredSignatureError:
                # Manejar token expirado
                return jsonify({'error': 'Token expired'}), 401
            except jwt.InvalidTokenError:
                # Manejar token inválido
                return jsonify({'error': 'Invalid token'}), 401
        else:
            # Si el encabezado de autorización no está en el formato esperado
            return jsonify({'error': 'Invalid authorization header format'}), 401
    else:
        # Si no se proporcionó un encabezado de autorización
        return jsonify({'error': 'Authorization header is missing'}), 401

@app.route('/cloud')
def cloud():
    # Obtener el token JWT de la cookie
    token = request.cookies.get('jwt_gallery')
    if token:
        try:
            # Verificar y decodificar el token JWT
            payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
            email = payload['email']
            user = User.query.filter_by(email=email).first()
            if user:
                # Obtener la lista de archivos del usuario
                user_folder = os.path.join(app.config['UPLOAD_FOLDER'], str(user.id))
                user_files = []
                if os.path.exists(user_folder):
                    user_files = os.listdir(user_folder)
                return render_template('cloud.html', files=user_files)
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')
    else:
        flash('Unauthorized access. Please log in.', 'danger')
    return redirect(url_for('login'))

@app.route('/view_file')
def view_file():
    filename = request.args.get('file')
    if filename:
        # Obtener el token JWT de la cookie
        token = request.cookies.get('jwt_gallery')
        if token:
            try:
                # Verificar y decodificar el token JWT
                payload = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
                email = payload['email']
                user = User.query.filter_by(email=email).first()
                if user:
                    # Cargar el archivo sin verificar si pertenece al usuario
                    user_folder = os.path.join(app.config['UPLOAD_FOLDER'], str(user.id))
                    filepath = os.path.join(user_folder, filename)
                    if os.path.exists(filepath):
                        # Leer el contenido del archivo
                        with open(filepath, 'r') as file:
                            content = file.read()
                        # Devolver el contenido del archivo como respuesta
                        return content, 200, {'Content-Type': 'text/plain; charset=utf-8'}
                    else:
                        flash('File not found.', 'danger')
            except jwt.ExpiredSignatureError:
                flash('Token expired. Please log in again.', 'danger')
            except jwt.InvalidTokenError:
                flash('Invalid token. Please log in again.', 'danger')
        else:
            flash('Unauthorized access. Please log in.', 'danger')
            return redirect(url_for('login'))
    else:
        flash('File name not provided.', 'danger')
    return redirect(url_for('cloud'))

# Ruta para manejar las solicitudes DELETE para eliminar archivos
@app.route('/delete_file', methods=['DELETE'])
def delete_file_route():
    file_name = request.args.get('file')
    if file_name:
        if delete_file(file_name):
            flash('File deleted successfully', 'success')
            return make_response("File deleted successfully", 200)
        else:
            flash('Failed to delete file', 'danger')
            return make_response("Failed to delete file", 500)
    else:
        flash('No file specified for deletion', 'danger')
        return make_response("No file specified for deletion", 400)

if __name__ == '__main__':
    if try_connect_to_db():
        with app.app_context():
            db.create_all()
            create_default_user()
        app.run(debug=True, host='0.0.0.0', port=5001)