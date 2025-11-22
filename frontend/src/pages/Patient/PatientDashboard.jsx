import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { FaCalendarAlt, FaPrescription, FaUserMd, FaHistory } from 'react-icons/fa';
import Layout from '../../components/Layout';
import { appointmentService } from '../../services/appointmentService';
import { useAuth } from '../../context/AuthContext';
import toast from 'react-hot-toast';

const PatientDashboard = () => {
  const { user } = useAuth();
  const [appointments, setAppointments] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchUpcomingAppointments();
  }, []);

  const fetchUpcomingAppointments = async () => {
    try {
      const data = await appointmentService.getUpcoming();
      setAppointments(data.data || data);
    } catch (error) {
      console.error('Error fetching appointments:', error);
      toast.error('Error al cargar las citas');
    } finally {
      setLoading(false);
    }
  };

  const stats = [
    { icon: <FaCalendarAlt />, label: 'Próximas Citas', value: appointments.length, color: 'bg-blue-500' },
    { icon: <FaPrescription />, label: 'Recetas Activas', value: '0', color: 'bg-green-500' },
    { icon: <FaUserMd />, label: 'Mi Doctor', value: '-', color: 'bg-purple-500' },
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
          <p className="text-gray-600 mt-2">Gestiona tus citas y consulta tu historial médico</p>
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
        <div className="grid md:grid-cols-2 gap-6 mb-8">
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.3 }}
          >
            <Link to="/patient/appointments/new" className="card hover:shadow-lg transition-shadow block">
              <div className="flex items-center space-x-4">
                <FaCalendarAlt className="text-4xl text-primary-600" />
                <div>
                  <h3 className="text-xl font-semibold text-gray-800">Agendar Cita</h3>
                  <p className="text-gray-600">Solicita una nueva cita médica</p>
                </div>
              </div>
            </Link>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.4 }}
          >
            <Link to="/patient/history" className="card hover:shadow-lg transition-shadow block">
              <div className="flex items-center space-x-4">
                <FaHistory className="text-4xl text-primary-600" />
                <div>
                  <h3 className="text-xl font-semibold text-gray-800">Historial Médico</h3>
                  <p className="text-gray-600">Consulta tu historial completo</p>
                </div>
              </div>
            </Link>
          </motion.div>
        </div>

        {/* Upcoming Appointments */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.5 }}
          className="card"
        >
          <h2 className="text-2xl font-bold text-gray-800 mb-4">Próximas Citas</h2>
          {loading ? (
            <div className="flex justify-center py-8">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>
          ) : appointments.length > 0 ? (
            <div className="space-y-4">
              {appointments.slice(0, 5).map((appointment) => (
                <div key={appointment.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div>
                    <p className="font-semibold text-gray-800">
                      {appointment.doctor?.user?.name || 'Doctor no asignado'}
                    </p>
                    <p className="text-sm text-gray-600">{appointment.reason}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-medium text-primary-600">
                      {new Date(appointment.appointment_date).toLocaleDateString('es-ES')}
                    </p>
                    <p className="text-sm text-gray-600">{appointment.appointment_time}</p>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-gray-600 text-center py-8">No tienes citas programadas</p>
          )}
        </motion.div>
      </div>
    </Layout>
  );
};

export default PatientDashboard;
