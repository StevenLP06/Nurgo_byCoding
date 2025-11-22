import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { FaCalendarAlt, FaUsers, FaExclamationTriangle, FaClipboardList } from 'react-icons/fa';
import Layout from '../../components/Layout';
import { appointmentService } from '../../services/appointmentService';
import { emergencyService } from '../../services/dataService';
import { useAuth } from '../../context/AuthContext';
import toast from 'react-hot-toast';

const DoctorDashboard = () => {
  const { user } = useAuth();
  const [appointments, setAppointments] = useState([]);
  const [emergencies, setEmergencies] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [appointmentsData, emergenciesData] = await Promise.all([
        appointmentService.getUpcoming(),
        emergencyService.getActive(),
      ]);
      setAppointments(appointmentsData.data || appointmentsData);
      setEmergencies(emergenciesData.data || emergenciesData);
    } catch (error) {
      console.error('Error fetching data:', error);
      toast.error('Error al cargar la información');
    } finally {
      setLoading(false);
    }
  };

  const stats = [
    { icon: <FaCalendarAlt />, label: 'Citas Hoy', value: appointments.length, color: 'bg-blue-500' },
    { icon: <FaUsers />, label: 'Pacientes', value: '0', color: 'bg-green-500' },
    { icon: <FaExclamationTriangle />, label: 'Emergencias', value: emergencies.length, color: 'bg-red-500' },
  ];

  return (
    <Layout>
      <div className="max-w-7xl mx-auto">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="text-3xl font-bold text-gray-800">Dr. {user?.name}</h1>
          <p className="text-gray-600 mt-2">Panel de control médico</p>
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

        {/* Emergencies Alert */}
        {emergencies.length > 0 && (
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            className="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg"
          >
            <div className="flex items-center">
              <FaExclamationTriangle className="text-red-500 text-2xl mr-4" />
              <div>
                <h3 className="font-bold text-red-800">Alertas de Emergencia</h3>
                <p className="text-red-700">Hay {emergencies.length} emergencia(s) que requieren atención</p>
              </div>
              <Link to="/doctor/emergencies" className="ml-auto btn-danger">
                Ver Emergencias
              </Link>
            </div>
          </motion.div>
        )}

        <div className="grid md:grid-cols-2 gap-6 mb-8">
          {/* Today's Appointments */}
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.3 }}
            className="card"
          >
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-xl font-bold text-gray-800">Citas de Hoy</h2>
              <Link to="/doctor/appointments" className="text-primary-600 hover:text-primary-700 text-sm">
                Ver todas
              </Link>
            </div>
            {loading ? (
              <div className="flex justify-center py-8">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
              </div>
            ) : appointments.length > 0 ? (
              <div className="space-y-3">
                {appointments.slice(0, 4).map((appointment) => (
                  <div key={appointment.id} className="p-3 bg-gray-50 rounded-lg">
                    <p className="font-semibold text-gray-800">
                      {appointment.patient?.user?.name || 'Paciente no asignado'}
                    </p>
                    <p className="text-sm text-gray-600">{appointment.reason}</p>
                    <p className="text-sm text-primary-600 mt-1">{appointment.appointment_time}</p>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-gray-600 text-center py-8">No hay citas programadas para hoy</p>
            )}
          </motion.div>

          {/* Quick Actions */}
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.4 }}
            className="card"
          >
            <h2 className="text-xl font-bold text-gray-800 mb-4">Acciones Rápidas</h2>
            <div className="space-y-3">
              <Link
                to="/doctor/patients"
                className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <FaUsers className="text-primary-600 text-xl" />
                <span className="font-medium text-gray-800">Ver Pacientes</span>
              </Link>
              <Link
                to="/doctor/prescriptions"
                className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <FaClipboardList className="text-primary-600 text-xl" />
                <span className="font-medium text-gray-800">Recetas Médicas</span>
              </Link>
              <Link
                to="/doctor/appointments"
                className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <FaCalendarAlt className="text-primary-600 text-xl" />
                <span className="font-medium text-gray-800">Gestionar Citas</span>
              </Link>
            </div>
          </motion.div>
        </div>
      </div>
    </Layout>
  );
};

export default DoctorDashboard;
