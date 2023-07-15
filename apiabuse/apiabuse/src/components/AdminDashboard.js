import React, { useEffect, useState } from 'react';
import jwt_decode from 'jwt-decode';
import { useNavigate } from 'react-router-dom';
import './AdminDashboard.css';

const AdminDashboard = () => {
  const [users, setUsers] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token');

    if (!token) {
      navigate('/login');
      return;
    }

    try {
      const decoded = jwt_decode(token);

      if (decoded.role !== 'admin') {
        navigate('/login');
        return;
      }

      // Obtener la lista de usuarios de la API
      // Reemplaza la URL con la ruta de tu API para obtener la lista de usuarios
      fetch('http://localhost:3001/api/v1/admin', {
        headers: {
          'Content-Type': 'application/json',
          'Cache-Control': 'no-cache',
          Authorization: `Bearer ${token}`,
        },
      })
        .then((response) => response.json())
        .then((data) => setUsers(data))
        .catch((error) => console.error('Error al obtener la lista de usuarios:', error));
    } catch (error) {
      navigate('/login');
      return;
    }
  }, [navigate]);

  return (
    <div>
      <h2>Admin Dashboard</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Email</th>
            {/* <th>Contraseña</th> */}
            <th>Intentos Fallidos</th>
            <th>Bloqueado</th>
            <th>Balance</th>
          </tr>
        </thead>
        <tbody>
          {users.map((user) => (
            <tr key={user.id}>
              <td>{user.id}</td>
              <td>{user.email}</td>
              {/* <td>{user.password}</td> */}
              <td>{user.failed_attempts}</td>
              <td>{user.blocked ? 'Sí' : 'No'}</td>
              <td>{user.balance}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default AdminDashboard;