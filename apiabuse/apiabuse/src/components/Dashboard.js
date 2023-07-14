import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import './Dashboard.css';

const Dashboard = () => {
  const [user, setUser] = useState(null);
  const [balance, setBalance] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate(); // Utiliza el hook useNavigate para la navegación

  useEffect(() => {
    async function fetchUser() {
      const token = localStorage.getItem('token');
      if (!token) {
        setLoading(false);
        return;
      }

      const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
        'Cache-Control': 'no-cache',
      };

      try {
        const response = await fetch('http://localhost:3001/api/v2/user', { headers });
        const data = await response.json(); // Analiza la respuesta como JSON
        setUser(data); // Almacena la información del usuario en el estado
        setBalance(data.balance); // Almacena el balance del usuario en el estado
      } catch (error) {
        console.error('Error al obtener la información del usuario:', error);
        setError(error.message); // Almacena el error en el estado
      } finally {
        setLoading(false);
      }
    }

    fetchUser();
  }, []);

  const handleLogout = () => {
    localStorage.removeItem('token'); // Elimina el token almacenado
    navigate('/login'); // Navega a la página de inicio de sesión
    window.location.reload();
  };

  if (error) {
    return <p className="dashboard-message">Error: {error}</p>;
  }
  
  if (loading) {
    return <p className="dashboard-message">Cargando...</p>;
  }
  
  if (!user) {
    return <p className="dashboard-message">Debe iniciar sesión para ver esta página</p>;
  }

  return (
    <div className="dashboard-container">
      <div className="dashboard-card">
        <h2>Información del usuario</h2>
        <label className="dashboard-label">ID:</label>
        <input className="dashboard-input" type="text" value={user.userId} readOnly />
        <label className="dashboard-label">Email:</label>
        <input className="dashboard-input" type="text" value={user.email} readOnly />
        <label className="dashboard-label">Balance:</label>
        <input className="dashboard-input" type="text" value={balance} readOnly />
        <button className="dashboard-button" onClick={handleLogout}>Cerrar sesión</button>
      </div>
    </div>
  );
};

export default Dashboard;