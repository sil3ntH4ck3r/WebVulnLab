from flask import Flask, render_template, url_for, flash, redirect, request, session, jsonify, make_response
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import create_engine
from sqlalchemy.exc import OperationalError
import time
import jwt
import random
import string
import requests
from http import HTTPStatus

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://username:password@printing_db:5432/printing_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Define the Gallery authorization endpoint and redirect URI
GALLERY_AUTHORIZATION_ENDPOINT = "http://localhost:5001/auth/callback"
GALLERY_REDIRECT_URI = "http://localhost:5001/gallery/photos"

db = SQLAlchemy(app)

class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password = db.Column(db.String(60), nullable=False)
    gallery_linked = db.Column(db.Boolean, default=False)
    client_id = db.Column(db.String(15), unique=True, nullable=False)

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

@app.route("/oauth/code", methods=['GET'])
def oauth_code():
    # Obtener el código de la URL
    code = request.args.get('codigo')
    redirect_uri = request.args.get('redirect_uri')

    # Check if the code was provided
    if code is None:
        return 'The code was not provided', 400

    if redirect_uri is None:
        return 'The redirect_uri was not provided', 400

    # Define the URL for the redirect
    redirect_url = 'http://localhost:5001/oauth/authorize/token'

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
            <!-- Agrega cualquier otro campo necesario aquí -->
        </form>
        </body>
        </html>
        """.format(url=redirect_url, code=code, redirect_uri=redirect_uri, client_id=client_id)

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
                # Si el usuario existe, obtener su client_id y utilizarlo en la URL de redirección
                return redirect(f'{GALLERY_AUTHORIZATION_ENDPOINT}?response_type=code&client_id={user.client_id}&redirect_uri={GALLERY_REDIRECT_URI}&scope=photos')
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')

    # Si el usuario no está autenticado o no se pudo obtener el client_id, redirigir al usuario al inicio de sesión
    return redirect(url_for('login'))

@app.route("/")
def home():
    return render_template('home.html')

@app.route("/register", methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username']
        email = request.form['email']
        password = request.form['password']

        # Generar un client_id aleatorio
        client_id = generate_client_id()

        # Crear un nuevo usuario con el client_id generado
        user = User(username=username, email=email, password=password, client_id=client_id)
        
        db.session.add(user)
        db.session.commit()
        flash('Account created! You can now link your gallery account.', 'success')
        return redirect(url_for('profile'))
    return render_template('register.html')

@app.route("/login", methods=['GET', 'POST'])
def login():
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
    # Obtener el token JWT de la cookie
    token = request.cookies.get('jwt_printing')
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

if __name__ == '__main__':
    if try_connect_to_db():
        with app.app_context():
            db.create_all()
        app.run(debug=True, host='0.0.0.0', port=5000)