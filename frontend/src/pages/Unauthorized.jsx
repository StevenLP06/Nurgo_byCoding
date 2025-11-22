import { Link } from 'react-router-dom';
import { FaExclamationTriangle } from 'react-icons/fa';

const Unauthorized = () => {
  return (
    <div className="min-h-screen bg-gray-50 flex items-center justify-center px-4">
      <div className="text-center">
        <FaExclamationTriangle className="text-6xl text-red-500 mx-auto mb-4" />
        <h1 className="text-4xl font-bold text-gray-800 mb-4">Acceso Denegado</h1>
        <p className="text-gray-600 mb-8">
          No tienes permisos para acceder a esta página.
        </p>
        <Link to="/dashboard" className="btn-primary">
          Volver al Dashboard
        </Link>
      </div>
    </div>
  );
};

export default Unauthorized;
