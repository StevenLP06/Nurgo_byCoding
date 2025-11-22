import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { FaExclamationCircle, FaUsers, FaCalendarAlt, FaHistory } from 'react-icons/fa';
import Layout from '../../components/Layout';
import { emergencyService } from '../../services/dataService';
import { useAuth } from '../../context/AuthContext';
import toast from 'react-hot-toast';

const GuardianDashboard = () => {
  const { user } = useAuth();
  const [showEmergencyModal, setShowEmergencyModal] = useState(false);
  const [emergencyData, setEmergencyData] = useState({
    patient_id: '',
    description: '',
    location: '',
  });
  const [loading, setLoading] = useState(false);

  const handleEmergency = async () => {
    if (!emergencyData.patient_id || !emergencyData.description || !emergencyData.location) {
      toast.error('Por favor completa todos los campos');
      return;
    }

    setLoading(true);
    try {
      await emergencyService.create(emergencyData);
      toast.success('Emergencia reportada. Un doctor será notificado inmediatamente.');
      setShowEmergencyModal(false);
      setEmergencyData({ patient_id: '', description: '', location: '' });
    } catch (error) {
      console.error('Error reporting emergency:', error);
      toast.error(error.response?.data?.message || 'Error al reportar emergencia');
    } finally {
      setLoading(false);
    }
  };

  const stats = [
    { icon: <FaUsers />, label: 'Pacientes a Cargo', value: '0', color: 'bg-blue-500' },
    { icon: <FaCalendarAlt />, label: 'Citas Programadas', value: '0', color: 'bg-green-500' },
    { icon: <FaExclamationCircle />, label: 'Emergencias', value: '0', color: 'bg-red-500' },
  ];

  return (
    <Layout>
      <div className="max-w-7xl mx-auto">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="text-3xl font-bold text-gray-800">Bienvenido, {user?.name}</h1>
          <p className="text-gray-600 mt-2">Panel de control del tutor</p>
        </motion.div>

        {/* Emergency Button - Prominent */}
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="mb-8"
        >
          <button
            onClick={() => setShowEmergencyModal(true)}
            className="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-6 rounded-xl shadow-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105"
          >
            <FaExclamationCircle className="inline-block text-4xl mb-2" />
            <h3 className="text-2xl font-bold">BOTÓN DE EMERGENCIA</h3>
            <p className="text-sm opacity-90 mt-1">Presiona aquí en caso de emergencia médica</p>
          </button>
        </motion.div>

        {/* Stats Cards */}
        <div className="grid md:grid-cols-3 gap-6 mb-8">
          {stats.map((stat, index) => (
            <motion.div
              key={index}
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: index * 0.1 }}
              className="card"
            >
              <div className="flex items-center space-x-4">
                <div className={`${stat.color} text-white p-4 rounded-lg text-2xl`}>
                  {stat.icon}
                </div>
                <div>
                  <p className="text-sm text-gray-600">{stat.label}</p>
                  <p className="text-2xl font-bold text-gray-800">{stat.value}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Quick Actions */}
        <div className="grid md:grid-cols-2 gap-6">
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.3 }}
          >
            <Link to="/guardian/patients" className="card hover:shadow-lg transition-shadow block">
              <div className="flex items-center space-x-4">
                <FaUsers className="text-4xl text-primary-600" />
                <div>
                  <h3 className="text-xl font-semibold text-gray-800">Mis Pacientes</h3>
                  <p className="text-gray-600">Gestiona a tus pacientes a cargo</p>
                </div>
              </div>
            </Link>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.4 }}
          >
            <Link to="/guardian/appointments" className="card hover:shadow-lg transition-shadow block">
              <div className="flex items-center space-x-4">
                <FaCalendarAlt className="text-4xl text-primary-600" />
                <div>
                  <h3 className="text-xl font-semibold text-gray-800">Citas Médicas</h3>
                  <p className="text-gray-600">Ver y agendar citas</p>
                </div>
              </div>
            </Link>
          </motion.div>
        </div>

        {/* Emergency Modal */}
        {showEmergencyModal && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              className="bg-white rounded-xl p-6 max-w-md w-full"
            >
              <div className="flex items-center space-x-3 mb-4">
                <FaExclamationCircle className="text-3xl text-red-500" />
                <h2 className="text-2xl font-bold text-gray-800">Reportar Emergencia</h2>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Paciente
                  </label>
                  <input
                    type="number"
                    value={emergencyData.patient_id}
                    onChange={(e) => setEmergencyData({ ...emergencyData, patient_id: e.target.value })}
                    className="input-field"
                    placeholder="ID del paciente"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Descripción de la Emergencia
                  </label>
                  <textarea
                    value={emergencyData.description}
                    onChange={(e) => setEmergencyData({ ...emergencyData, description: e.target.value })}
                    className="input-field"
                    rows="3"
                    placeholder="Describe la situación de emergencia"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Ubicación
                  </label>
                  <input
                    type="text"
                    value={emergencyData.location}
                    onChange={(e) => setEmergencyData({ ...emergencyData, location: e.target.value })}
                    className="input-field"
                    placeholder="Dirección exacta"
                  />
                </div>
              </div>

              <div className="flex space-x-3 mt-6">
                <button
                  onClick={() => setShowEmergencyModal(false)}
                  className="btn-secondary flex-1"
                  disabled={loading}
                >
                  Cancelar
                </button>
                <button
                  onClick={handleEmergency}
                  className="btn-danger flex-1"
                  disabled={loading}
                >
                  {loading ? 'Reportando...' : 'Reportar Emergencia'}
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default GuardianDashboard;
