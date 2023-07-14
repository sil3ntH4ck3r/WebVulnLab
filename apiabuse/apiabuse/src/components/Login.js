import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import './Login.css';

const Login = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loginResponse, setLoginResponse] = useState(null);
  const navigate = useNavigate(); // Utiliza el hook useNavigate para la navegación

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      navigate('/dashboard');
    }
  }, [navigate]);

  async function handleSubmit(event) {
    event.preventDefault();
    try {
      const response = await fetch('http://localhost:3001/api/v2/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });
      if (response.ok) {
        const data = await response.json();
        setLoginResponse({ type: 'success', message: data.message }); // Almacena la respuesta de éxito en el estado
        localStorage.setItem('token', data.token); // Almacena el token JWT en el almacenamiento local del navegador
        navigate('/dashboard');
        window.location.reload(); // Hace un refresh de la página
      } else {
        const errorData = await response.json();
        setLoginResponse({ type: 'error', message: errorData.error }); // Almacena el error en el estado
      }
    } catch (error) {
      console.error('Error al iniciar sesión:', error);
    }
  }

  function handleOkClick() {
    setLoginResponse(null); // Limpia el estado del mensaje
  }

  return (
    <div className="login-container">
    <h2>Iniciar sesión</h2>
    {loginResponse && (
        <div
        className={`${
            loginResponse.type === 'success' ? 'success-dialog' : 'error-dialog'
        }`}
        >
        <p>{loginResponse.message}</p>
        {loginResponse.token && <p>Authorization: {loginResponse.token}</p>}
        <button onClick={handleOkClick}>OK</button>
        </div>
    )}
    <form onSubmit={handleSubmit}>
        <div className="form-group">
        <label htmlFor="email">Email:</label>
        <input
            type="text"
            id="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
        />
        </div>
        <div className="form-group">
        <label htmlFor="password">Contraseña:</label>
        <input
            type="password"
            id="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
        />
        </div>
        <button type="submit">Iniciar sesión</button>
        <div className="forgot-password">
            <a href="/changePassword">He olvidado la contraseña</a>
        </div>
    </form>
    </div>
  );
};

export default Login;