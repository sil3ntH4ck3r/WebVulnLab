from flask import Flask, render_template, url_for, flash, redirect, request, session, jsonify, make_response
from flask_sqlalchemy import SQLAlchemy
from flask_login import UserMixin
from sqlalchemy import create_engine
from datetime import datetime
from sqlalchemy.exc import OperationalError
from werkzeug.utils import secure_filename
import base64
import time
import jwt

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://username:password@gallery_db:5432/gallery_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password = db.Column(db.String(60), nullable=False)
    images = db.relationship('Image', backref='user', lazy=True)

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

@app.route("/")
def home():
    return render_template('home.html')

@app.route("/register", methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username']
        email = request.form['email']
        password = request.form['password']
        user = User(username=username, email=email, password=password)
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
            token = jwt.encode({'email': user.email}, app.config['SECRET_KEY'], algorithm='HS256')

            # Crear una respuesta
            response = make_response(redirect(url_for('profile')))
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
                    
                    # No need to check the file extension since we're storing the image in the database
                    image_data = file.read()
                    new_image = Image(filename=file.filename, data=image_data, user_id=user.id)
                    db.session.add(new_image)
                    db.session.commit()
                    flash('File uploaded successfully', 'success')
                    return redirect(url_for('gallery'))
            except jwt.ExpiredSignatureError:
                flash('Token expired. Please log in again.', 'danger')
            except jwt.InvalidTokenError:
                flash('Invalid token. Please log in again.', 'danger')
        else:
            flash('Unauthorized access. Please log in.', 'danger')
            return redirect(url_for('login'))

    return render_template('upload.html')

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
                images = [{'filename': img.filename, 'data': base64.b64encode(img.data).decode('ascii')} for img in user.images]
                return render_template('gallery.html', images=images)
        except jwt.ExpiredSignatureError:
            flash('Token expired. Please log in again.', 'danger')
        except jwt.InvalidTokenError:
            flash('Invalid token. Please log in again.', 'danger')

    # Si el token es inválido o no se proporciona, redirigir al usuario al inicio de sesión
    flash('Unauthorized access. Please log in.', 'danger')
    return redirect(url_for('login'))

if __name__ == '__main__':
    if try_connect_to_db():
        with app.app_context():
            db.create_all()
        app.run(debug=True, host='0.0.0.0', port=5001)