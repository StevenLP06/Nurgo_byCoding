import api from './api';

export const appointmentService = {
  // Get all appointments
  getAll: async (params = {}) => {
    const response = await api.get('/appointments', { params });
    return response.data;
  },

  // Get single appointment
  getById: async (id) => {
    const response = await api.get(`/appointments/${id}`);
    return response.data;
  },

  // Create appointment
  create: async (data) => {
    const response = await api.post('/appointments', data);
    return response.data;
  },

  // Update appointment
  update: async (id, data) => {
    const response = await api.put(`/appointments/${id}`, data);
    return response.data;
  },

  // Delete appointment
  delete: async (id) => {
    const response = await api.delete(`/appointments/${id}`);
    return response.data;
  },

  // Get upcoming appointments
  getUpcoming: async () => {
    const response = await api.get('/appointments-upcoming');
    return response.data;
  },

  // Get appointments by doctor
  getByDoctor: async (doctorId) => {
    const response = await api.get(`/appointments?doctor_id=${doctorId}`);
    return response.data;
  },

  // Get appointments by patient
  getByPatient: async (patientId) => {
    const response = await api.get(`/appointments?patient_id=${patientId}`);
    return response.data;
  },
};
