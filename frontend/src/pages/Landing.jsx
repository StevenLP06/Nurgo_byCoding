import { Link } from 'react-router-dom';
import { FaHeartbeat, FaUserMd, FaCalendarCheck, FaHome, FaPrescription } from 'react-icons/fa';
import { motion } from 'framer-motion';

const Landing = () => {
  const services = [
    {
      icon: <FaCalendarCheck className="text-4xl text-primary-600" />,
      title: 'Gestión de Citas',
      description: 'Agenda y gestiona citas médicas de forma rápida y sencilla',
    },
    {
      icon: <FaUserMd className="text-4xl text-primary-600" />,
      title: 'Equipo Médico',
      description: 'Profesionales altamente calificados para tu atención',
    },
    {
      icon: <FaPrescription className="text-4xl text-primary-600" />,
      title: 'Recetas Médicas',
      description: 'Gestión digital de medicamentos y prescripciones',
    },
    {
      icon: <FaHome className="text-4xl text-primary-600" />,
      title: 'Visitas a Domicilio',
      description: 'Atención médica en la comodidad de tu hogar',
    },
  ];

  const testimonials = [
    {
      name: 'María González',
      role: 'Paciente',
      comment: 'Excelente servicio, la atención es muy profesional y el sistema es muy fácil de usar.',
      rating: 5,
    },
    {
      name: 'Carlos Ramírez',
      role: 'Tutor',
      comment: 'Me siento muy tranquilo sabiendo que puedo gestionar las citas de mis hijos desde casa.',
      rating: 5,
    },
    {
      name: 'Ana Martínez',
      role: 'Paciente',
      comment: 'Las visitas a domicilio son muy convenientes, especialmente para adultos mayores.',
      rating: 5,
    },
  ];

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2,
      },
    },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.5 },
    },
  };

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-20">
        <div className="container mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: -30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-center"
          >
            <FaHeartbeat className="text-6xl mx-auto mb-6" />
            <h1 className="text-5xl font-bold mb-4">Nurgo Health</h1>
            <p className="text-xl mb-8">Sistema integral de gestión de salud</p>
            <div className="space-x-4">
              <Link to="/login" className="btn-primary bg-white text-primary-600 hover:bg-gray-100">
                Iniciar Sesión
              </Link>
              <Link to="/register" className="btn-secondary border-white text-white hover:bg-white hover:text-primary-600">
                Registrarse
              </Link>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Services Section */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <motion.h2
            initial={{ opacity: 0 }}
            whileInView={{ opacity: 1 }}
            viewport={{ once: true }}
            className="text-3xl font-bold text-center mb-12"
          >
            Nuestros Servicios
          </motion.h2>
          <motion.div
            variants={containerVariants}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true }}
            className="grid md:grid-cols-2 lg:grid-cols-4 gap-8"
          >
            {services.map((service, index) => (
              <motion.div
                key={index}
                variants={itemVariants}
                className="card text-center hover:shadow-xl transition-shadow"
              >
                <div className="flex justify-center mb-4">{service.icon}</div>
                <h3 className="text-xl font-semibold mb-2">{service.title}</h3>
                <p className="text-gray-600">{service.description}</p>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <motion.h2
            initial={{ opacity: 0 }}
            whileInView={{ opacity: 1 }}
            viewport={{ once: true }}
            className="text-3xl font-bold text-center mb-12"
          >
            Opiniones de Clientes
          </motion.h2>
          <motion.div
            variants={containerVariants}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true }}
            className="grid md:grid-cols-3 gap-8"
          >
            {testimonials.map((testimonial, index) => (
              <motion.div key={index} variants={itemVariants} className="card">
                <div className="flex items-center mb-2">
                  {[...Array(testimonial.rating)].map((_, i) => (
                    <span key={i} className="text-yellow-400">★</span>
                  ))}
                </div>
                <p className="text-gray-700 mb-4 italic">"{testimonial.comment}"</p>
                <div>
                  <p className="font-semibold">{testimonial.name}</p>
                  <p className="text-sm text-gray-500">{testimonial.role}</p>
                </div>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* About Section */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="max-w-3xl mx-auto text-center"
          >
            <h2 className="text-3xl font-bold mb-6">Sobre Nosotros</h2>
            <p className="text-gray-700 mb-4">
              Nurgo Health es una plataforma integral de gestión de salud que conecta a pacientes,
              médicos y tutores en un solo lugar. Nuestro objetivo es facilitar el acceso a servicios
              de salud de calidad mediante la tecnología.
            </p>
            <p className="text-gray-700">
              Con funciones como gestión de citas, recetas médicas digitales, visitas a domicilio y
              alertas de emergencia, nos comprometemos a mejorar la experiencia de atención médica
              para todos.
            </p>
          </motion.div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-800 text-white py-8">
        <div className="container mx-auto px-4 text-center">
          <p>&copy; 2025 Nurgo Health. Todos los derechos reservados.</p>
          <p className="text-sm text-gray-400 mt-2">Sistema de Gestión de Salud</p>
        </div>
      </footer>
    </div>
  );
};

export default Landing;
