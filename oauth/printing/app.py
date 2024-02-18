from flask import Flask, render_template, url_for, flash, redirect, request, session, jsonify, make_response
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import create_engine
from sqlalchemy.exc import OperationalError
import time
import jwt

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://username:password@printing_db:5432/printing_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password = db.Column(db.String(60), nullable=False)
    gallery_linked = db.Column(db.Boolean, default=False)

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