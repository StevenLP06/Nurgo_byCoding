import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { FaUserMd, FaUsers, FaCalendarAlt, FaExclamationTriangle } from 'react-icons/fa';
import Layout from '../../components/Layout';
import { useAuth } from '../../context/AuthContext';

const AdminDashboard = () => {
  const { user } = useAuth();

  const stats = [
    { icon: <FaUserMd />, label: 'Doctores', value: '0', color: 'bg-blue-500', link: '/admin/doctors' },
    { icon: <FaUsers />, label: 'Pacientes', value: '0', color: 'bg-green-500', link: '/admin/patients' },
    { icon: <FaCalendarAlt />, label: 'Citas Totales', value: '0', color: 'bg-purple-500', link: '/admin/appointments' },
    { icon: <FaExclamationTriangle />, label: 'Emergencias', value: '0', color: 'bg-red-500', link: '/admin/emergencies' },
  ];

  const quickActions = [
    { title: 'Gestionar Doctores', description: 'Ver y administrar doctores', link: '/admin/doctors', icon: <FaUserMd /> },
    { title: 'Gestionar Pacientes', description: 'Ver y administrar pacientes', link: '/admin/patients', icon: <FaUsers /> },
    { title: 'Ver Citas', description: 'Administrar todas las citas', link: '/admin/appointments', icon: <FaCalendarAlt /> },
    { title: 'Emergencias', description: 'Monitorear emergencias', link: '/admin/emergencies', icon: <FaExclamationTriangle /> },
  ];

  return (
    <Layout>
      <div className="max-w-7xl mx-auto">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="text-3xl font-bold text-gray-800">Panel de Administración</h1>
          <p className="text-gray-600 mt-2">Bienvenido, {user?.name}</p>
        </motion.div>

        {/* Stats Cards */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <motion.div
              key={index}
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: index * 0.1 }}
            >
              <Link to={stat.link} className="card hover:shadow-lg transition-shadow block">
                <div className="flex items-center space-x-4">
                  <div className={`${stat.color} text-white p-4 rounded-lg text-2xl`}>
                    {stat.icon}
                  </div>
                  <div>
                    <p className="text-sm text-gray-600">{stat.label}</p>
                    <p className="text-2xl font-bold text-gray-800">{stat.value}</p>
                  </div>
                </div>
              </Link>
            </motion.div>
          ))}
        </div>

        {/* Quick Actions */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4 }}
          className="card"
        >
          <h2 className="text-2xl font-bold text-gray-800 mb-6">Acciones Rápidas</h2>
          <div className="grid md:grid-cols-2 gap-4">
            {quickActions.map((action, index) => (
              <Link
                key={index}
                to={action.link}
                className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="text-primary-600 text-2xl">
                  {action.icon}
                </div>
                <div>
                  <h3 className="font-semibold text-gray-800">{action.title}</h3>
                  <p className="text-sm text-gray-600">{action.description}</p>
                </div>
              </Link>
            ))}
          </div>
        </motion.div>

        {/* System Overview */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.5 }}
          className="card mt-6"
        >
          <h2 className="text-2xl font-bold text-gray-800 mb-4">Resumen del Sistema</h2>
          <div className="space-y-3">
            <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span className="text-gray-700">Estado del Sistema</span>
              <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                Operativo
              </span>
            </div>
            <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span className="text-gray-700">Citas Hoy</span>
              <span className="font-semibold text-gray-800">0</span>
            </div>
            <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span className="text-gray-700">Doctores Activos</span>
              <span className="font-semibold text-gray-800">0</span>
            </div>
          </div>
        </motion.div>
      </div>
    </Layout>
  );
};

export default AdminDashboard;
