import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { motion } from 'framer-motion';
import { FaHeartbeat, FaUser, FaEnvelope, FaLock, FaPhone, FaIdCard, FaBirthdayCake } from 'react-icons/fa';
import toast from 'react-hot-toast';

const Register = () => {
  const navigate = useNavigate();
  const { register } = useAuth();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    document_number: '',
    birth_date: '',
    role_name: 'patient',
    // Campos específicos por rol
    specialty: '',
    license_number: '',
    relationship: 'parent',
  });
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (formData.password !== formData.password_confirmation) {
      toast.error('Las contraseñas no coinciden');
      return;
    }

    setLoading(true);

    try {
      // Preparar datos según el rol
      const dataToSend = {
        name: formData.name,
        email: formData.email,
        password: formData.password,
        password_confirmation: formData.password_confirmation,
        phone: formData.phone,
        document_number: formData.document_number,
        birth_date: formData.birth_date,
        role_name: formData.role_name,
      };

      // Agregar campos específicos según el rol
      if (formData.role_name === 'doctor') {
        dataToSend.specialty = formData.specialty;
        dataToSend.license_number = formData.license_number;
      } else if (formData.role_name === 'guardian') {
        dataToSend.relationship = formData.relationship;
      }

      await register(dataToSend);
      toast.success('Registro exitoso');
      navigate('/dashboard');
    } catch (error) {
      console.error('Register error:', error);
      const errorMessage = error.response?.data?.message || 'Error al registrarse';
      const errors = error.response?.data?.errors;
      
      if (errors) {
        Object.values(errors).flat().forEach(err => toast.error(err));
      } else {
        toast.error(errorMessage);
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center px-4 py-8">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        className="card max-w-2xl w-full"
      >
        <div className="text-center mb-8">
          <FaHeartbeat className="text-5xl text-primary-600 mx-auto mb-4" />
          <h2 className="text-3xl font-bold text-gray-800">Crear Cuenta</h2>
          <p className="text-gray-600 mt-2">Únete a Nurgo Health</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid md:grid-cols-2 gap-6">
            {/* Name */}
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                Nombre Completo
              </label>
              <div className="relative">
                <FaUser className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="text"
                  id="name"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  required
                  className="input-field pl-10"
                  placeholder="Juan Pérez"
                />
              </div>
            </div>

            {/* Email */}
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                Correo Electrónico
              </label>
              <div className="relative">
                <FaEnvelope className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                  className="input-field pl-10"
                  placeholder="correo@ejemplo.com"
                />
              </div>
            </div>

            {/* Phone */}
            <div>
              <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-2">
                Teléfono
              </label>
              <div className="relative">
                <FaPhone className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  required
                  className="input-field pl-10"
                  placeholder="3001234567"
                />
              </div>
            </div>

            {/* Identification Number */}
            <div>
              <label htmlFor="document_number" className="block text-sm font-medium text-gray-700 mb-2">
                Número de Identificación
              </label>
              <div className="relative">
                <FaIdCard className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="text"
                  id="document_number"
                  name="document_number"
                  value={formData.document_number}
                  onChange={handleChange}
                  required
                  className="input-field pl-10"
                  placeholder="1234567890"
                />
              </div>
            </div>

            {/* Date of Birth */}
            <div>
              <label htmlFor="birth_date" className="block text-sm font-medium text-gray-700 mb-2">
                Fecha de Nacimiento
              </label>
              <div className="relative">
                <FaBirthdayCake className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="date"
                  id="birth_date"
                  name="birth_date"
                  value={formData.birth_date}
                  onChange={handleChange}
                  required
                  max={new Date().toISOString().split('T')[0]}
                  className="input-field pl-10"
                />
              </div>
            </div>

            {/* Role */}
            <div>
              <label htmlFor="role_name" className="block text-sm font-medium text-gray-700 mb-2">
                Tipo de Usuario
              </label>
              <select
                id="role_name"
                name="role_name"
                value={formData.role_name}
                onChange={handleChange}
                required
                className="input-field"
              >
                <option value="patient">Paciente</option>
                <option value="guardian">Tutor</option>
                <option value="doctor">Doctor</option>
              </select>
            </div>

            {/* Conditional Fields for Doctor */}
            {formData.role_name === 'doctor' && (
              <>
                <div>
                  <label htmlFor="specialty" className="block text-sm font-medium text-gray-700 mb-2">
                    Especialidad
                  </label>
                  <input
                    type="text"
                    id="specialty"
                    name="specialty"
                    value={formData.specialty}
                    onChange={handleChange}
                    required
                    className="input-field"
                    placeholder="Medicina General"
                  />
                </div>
                <div>
                  <label htmlFor="license_number" className="block text-sm font-medium text-gray-700 mb-2">
                    Número de Licencia
                  </label>
                  <input
                    type="text"
                    id="license_number"
                    name="license_number"
                    value={formData.license_number}
                    onChange={handleChange}
                    required
                    className="input-field"
                    placeholder="LIC-123456"
                  />
                </div>
              </>
            )}

            {/* Conditional Fields for Guardian */}
            {formData.role_name === 'guardian' && (
              <div>
                <label htmlFor="relationship" className="block text-sm font-medium text-gray-700 mb-2">
                  Relación con el Paciente
                </label>
                <select
                  id="relationship"
                  name="relationship"
                  value={formData.relationship}
                  onChange={handleChange}
                  required
                  className="input-field"
                >
                  <option value="parent">Padre/Madre</option>
                  <option value="spouse">Esposo/Esposa</option>
                  <option value="sibling">Hermano/Hermana</option>
                  <option value="child">Hijo/Hija</option>
                  <option value="other">Otro</option>
                </select>
              </div>
            )}

            {/* Password */}
            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                Contraseña
              </label>
              <div className="relative">
                <FaLock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="password"
                  id="password"
                  name="password"
                  value={formData.password}
                  onChange={handleChange}
                  required
                  minLength="8"
                  className="input-field pl-10"
                  placeholder="••••••••"
                />
              </div>
            </div>

            {/* Password Confirmation */}
            <div>
              <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-2">
                Confirmar Contraseña
              </label>
              <div className="relative">
                <FaLock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="password"
                  id="password_confirmation"
                  name="password_confirmation"
                  value={formData.password_confirmation}
                  onChange={handleChange}
                  required
                  minLength="8"
                  className="input-field pl-10"
                  placeholder="••••••••"
                />
              </div>
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="btn-primary w-full"
          >
            {loading ? 'Cargando...' : 'Registrarse'}
          </button>
        </form>

        <div className="mt-6 text-center">
          <p className="text-gray-600">
            ¿Ya tienes una cuenta?{' '}
            <Link to="/login" className="text-primary-600 hover:text-primary-700 font-medium">
              Inicia sesión aquí
            </Link>
          </p>
          <Link to="/" className="block mt-4 text-gray-500 hover:text-gray-700 text-sm">
            Volver al inicio
          </Link>
        </div>
      </motion.div>
    </div>
  );
};

export default Register;
