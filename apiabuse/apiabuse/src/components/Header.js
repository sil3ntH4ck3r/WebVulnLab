import React from 'react';
import { NavLink } from 'react-router-dom';
import './Header.css';

const Header = () => {
  const token = localStorage.getItem('token'); // Obtén el token JWT almacenado

  return (
    <header>
      <NavLink to="/" className="logo">API Abuse</NavLink>
      <nav className="nav-right">
        {token ? (
          <>
            <NavLink to="/dashboard" className="button">Dashboard</NavLink>
            <NavLink to="/shop" className="button">Shop</NavLink>
          </>
        ) : (
          <>
            <NavLink to="/register" className="button">Registrarse</NavLink>
            <NavLink to="/login" className="button">Iniciar sesión</NavLink>
          </>
        )}
      </nav>
    </header>
  );
};

export default Header;