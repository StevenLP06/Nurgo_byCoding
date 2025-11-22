import api from './api';

export const authService = {
  // Register new user
  register: async (userData) => {
    const response = await api.post('/register', userData);
    if (response.data.token) {
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    return response.data;
  },

  // Login
  login: async (credentials) => {
    const response = await api.post('/login', credentials);
    if (response.data.token || response.data.data?.access_token) {
      const token = response.data.token || response.data.data.access_token;
      const user = response.data.user || response.data.data.user;
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
    }
    return response.data;
  },

  // Logout
  logout: async () => {
    try {
      await api.post('/logout');
    } finally {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  },

  // Get current user
  me: async () => {
    const response = await api.get('/me');
    localStorage.setItem('user', JSON.stringify(response.data));
    return response.data;
  },

  // Get stored user
  getCurrentUser: () => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  // Check if user is authenticated
  isAuthenticated: () => {
    return !!localStorage.getItem('token');
  },
};
