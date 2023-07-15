import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Header from './components/Header';
import Register from './components/Register';
import Login from './components/Login';
import Footer from './components/Footer';
import Dashboard from './components/Dashboard';
import ChangePassword from './components/ChangePassword';
import Shop from './components/Shop';
import Home from './components/Home';
import AdminDashboard from './components/AdminDashboard';

function App() {
  return (
    <Router>
      <Header />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/register" element={<Register />} />
        <Route path="/login" element={<Login />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/changePassword" element={<ChangePassword />} />
        <Route path="/Shop" element={<Shop />} />
        <Route path="/adminDashboard" element={<AdminDashboard />} />
      </Routes>
      <Footer />
    </Router>
  );
}

export default App;