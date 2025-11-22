import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const Dashboard = () => {
  const { user, loading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!loading && user) {
      const userRole = user.role_name || user.role?.name;
      
      switch (userRole) {
        case 'admin':
          navigate('/admin/dashboard', { replace: true });
          break;
        case 'doctor':
          navigate('/doctor/dashboard', { replace: true });
          break;
        case 'patient':
          navigate('/patient/dashboard', { replace: true });
          break;
        case 'guardian':
          navigate('/guardian/dashboard', { replace: true });
          break;
        default:
          console.log('Unknown role:', userRole, user);
          navigate('/unauthorized', { replace: true });
      }
    }
  }, [user, loading, navigate]);

  return (
    <div className="min-h-screen flex items-center justify-center">
      <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
    </div>
  );
};

export default Dashboard;
