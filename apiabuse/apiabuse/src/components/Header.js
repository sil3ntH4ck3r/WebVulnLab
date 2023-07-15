import React from 'react';
import { NavLink } from 'react-router-dom';
import jwt_decode from 'jwt-decode'; // Importa la librería jwt-decode para decodificar el token JWT
import './Header.css';

const Header = () => {
  const token = localStorage.getItem('token'); // Obtén el token JWT almacenado
  let isAdmin = false; // Inicializa la variable isAdmin en falso

  if (token) {
    const decodedToken = jwt_decode(token); // Decodifica el token JWT
    isAdmin = decodedToken.role === 'admin'; // Verifica si el campo role tiene el valor admin
  }

  return (
    <header>
      <NavLink to="/" className="logo">API Abuse</NavLink>
      <nav className="nav-right">
        {token ? (
          <>
            <NavLink to="/dashboard" className="button">Dashboard</NavLink>
            <NavLink to="/shop" className="button">Shop</NavLink>
            {isAdmin && <NavLink to="/adminDashboard" className="button">Admin</NavLink>} {/* Muestra el botón solo si isAdmin es verdadero */}
          </>
        ) : (
          <>
            <NavLink to="/register" className="button">Registrarse</NavLink>
            <NavLink to="/login" className="button">Iniciar sesión</NavLink>
          </>
        )}
        <NavLink to="http://mail.local" className="button">Mail</NavLink>
      </nav>
    </header>
  );
};

export default Header;