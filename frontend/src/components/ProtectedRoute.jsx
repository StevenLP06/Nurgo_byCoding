import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const ProtectedRoute = ({ children, allowedRoles }) => {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  // Get role name from user object (supports both role_name and role.name)
  const userRole = user.role_name || user.role?.name;

  if (allowedRoles && userRole && !allowedRoles.includes(userRole)) {
    console.log('Access denied:', { userRole, allowedRoles, user });
    return <Navigate to="/unauthorized" replace />;
  }

  return children;
};

export default ProtectedRoute;
