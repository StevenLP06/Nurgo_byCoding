import api from './api';

export const doctorService = {
  getAll: async (params = {}) => {
    const response = await api.get('/doctors', { params });
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/doctors/${id}`);
    return response.data;
  },

  create: async (data) => {
    const response = await api.post('/doctors', data);
    return response.data;
  },

  update: async (id, data) => {
    const response = await api.put(`/doctors/${id}`, data);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/doctors/${id}`);
    return response.data;
  },

  getAvailable: async () => {
    const response = await api.get('/doctors-available');
    return response.data;
  },
};

export const patientService = {
  getAll: async (params = {}) => {
    const response = await api.get('/patients', { params });
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/patients/${id}`);
    return response.data;
  },

  create: async (data) => {
    const response = await api.post('/patients', data);
    return response.data;
  },

  update: async (id, data) => {
    const response = await api.put(`/patients/${id}`, data);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/patients/${id}`);
    return response.data;
  },

  getMedicalHistory: async (id) => {
    const response = await api.get(`/patients/${id}/medical-history`);
    return response.data;
  },
};

export const emergencyService = {
  getAll: async (params = {}) => {
    const response = await api.get('/emergencies', { params });
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/emergencies/${id}`);
    return response.data;
  },

  create: async (data) => {
    const response = await api.post('/emergencies', data);
    return response.data;
  },

  update: async (id, data) => {
    const response = await api.put(`/emergencies/${id}`, data);
    return response.data;
  },

  getActive: async () => {
    const response = await api.get('/emergencies-active');
    return response.data;
  },
};
