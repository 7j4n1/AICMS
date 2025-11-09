import axios from 'utils/axios';

/**
* AICMS API Service Layer
* Provides methods to interact with Laravel backend API
*/

// ==================== Authentication ====================

export const authAPI = {
 login: (credentials) => axios.post('/auth/login', credentials),
 register: (userData) => axios.post('/auth/register', userData),
 resetPassword: (data) => axios.post('/auth/reset-password', data),
 getProfile: () => axios.get('/account/profile'),
};

// ==================== Account/Profile ====================

export const accountAPI = {
 getProfile: () => axios.get('/account/profile'),
 getBalance: () => axios.get('/account/balance'),
 getSavings: () => axios.get('/account/savings'),
 getShares: () => axios.get('/account/shares'),
 downloadLedger: (data) => axios.post('/account/download-ledger', data),
};

// ==================== Members Management ====================

export const membersAPI = {
 getAll: (params) => axios.get('/members', { params }),
 getById: (id) => axios.get(`/members/${id}`),
 getByCoopId: (coopId) => axios.get(`/members/coop/${coopId}`),
 create: (data) => axios.post('/members', data),
 update: (id, data) => axios.put(`/members/${id}`, data),
 delete: (id) => axios.delete(`/members/${id}`),
};

// ==================== Loans Management ====================

export const loansAPI = {
 getAll: (params) => axios.get('/loans', { params }),
 getById: (id) => axios.get(`/loans/${id}`),
 getActive: (params) => axios.get('/active-loans', { params }),
 create: (data) => axios.post('/loans', data),
 update: (id, data) => axios.put(`/loans/${id}`, data),
 delete: (id) => axios.delete(`/loans/${id}`),
 complete: (id) => axios.post(`/loans/${id}/complete`),
};

// ==================== Payments Management ====================

export const paymentsAPI = {
 getAll: (params) => axios.get('/payments', { params }),
 getById: (id) => axios.get(`/payments/${id}`),
 create: (data) => axios.post('/payments', data),
 update: (id, data) => axios.put(`/payments/${id}`, data),
 delete: (id) => axios.delete(`/payments/${id}`),
};

// ==================== Annual Fees Management ====================

export const annualFeesAPI = {
 getAll: (params) => axios.get('/annual-fees', { params }),
 getById: (id) => axios.get(`/annual-fees/${id}`),
 create: (data) => axios.post('/annual-fees', data),
 update: (id, data) => axios.put(`/annual-fees/${id}`, data),
 delete: (id) => axios.delete(`/annual-fees/${id}`),
};

// ==================== Admin Management ====================

export const adminsAPI = {
 getAll: (params) => axios.get('/admins', { params }),
 getById: (id) => axios.get(`/admins/${id}`),
 create: (data) => axios.post('/admins', data),
 update: (id, data) => axios.put(`/admins/${id}`, data),
 delete: (id) => axios.delete(`/admins/${id}`),
};

// ==================== Categories Management ====================

export const categoriesAPI = {
 getAll: (params) => axios.get('/categories', { params }),
 getById: (id) => axios.get(`/categories/${id}`),
 create: (data) => axios.post('/categories', data),
 update: (id, data) => axios.put(`/categories/${id}`, data),
 delete: (id) => axios.delete(`/categories/${id}`),
};

// ==================== Items Management ====================

export const itemsAPI = {
 getAll: (params) => axios.get('/items', { params }),
 getById: (id) => axios.get(`/items/${id}`),
 create: (data) => axios.post('/items', data),
 update: (id, data) => axios.put(`/items/${id}`, data),
 delete: (id) => axios.delete(`/items/${id}`),
};

// ==================== System Configuration ====================

export const configurationAPI = {
 getAll: () => axios.get('/configurations'),
 update: (configurations) => axios.post('/configurations', { configurations }),
 uploadLogo: (formData) => axios.post('/configurations/logo', formData, {
   headers: { 'Content-Type': 'multipart/form-data' }
 }),
 getLogo: () => axios.get('/configurations/logo'),
};

// ==================== Payment Gateways ====================

export const paymentGatewaysAPI = {
 getAll: (params) => axios.get('/payment-gateways', { params }),
 getById: (id) => axios.get(`/payment-gateways/${id}`),
 create: (data) => axios.post('/payment-gateways', data),
 update: (id, data) => axios.put(`/payment-gateways/${id}`, data),
 delete: (id) => axios.delete(`/payment-gateways/${id}`),
};

// ==================== Savings Types ====================

export const savingsTypesAPI = {
 getAll: (params) => axios.get('/savings-types', { params }),
 getById: (id) => axios.get(`/savings-types/${id}`),
 create: (data) => axios.post('/savings-types', data),
 update: (id, data) => axios.put(`/savings-types/${id}`, data),
 delete: (id) => axios.delete(`/savings-types/${id}`),
};

// ==================== Loan Eligibility ====================

export const loanEligibilityAPI = {
 getAll: () => axios.get('/loan-eligibility-settings'),
 getActive: () => axios.get('/loan-eligibility/active'),
 getById: (id) => axios.get(`/loan-eligibility-settings/${id}`),
 create: (data) => axios.post('/loan-eligibility-settings', data),
 update: (id, data) => axios.put(`/loan-eligibility-settings/${id}`, data),
 delete: (id) => axios.delete(`/loan-eligibility-settings/${id}`),
 calculate: (coopId) => axios.post('/loan-eligibility/calculate', { coopId }),
};

// ==================== Support Tickets ====================

export const supportTicketsAPI = {
 getAll: (params) => axios.get('/support-tickets', { params }),
 getById: (id) => axios.get(`/support-tickets/${id}`),
 create: (formData) => axios.post('/support-tickets', formData, {
   headers: { 'Content-Type': 'multipart/form-data' }
 }),
 update: (id, data) => axios.put(`/support-tickets/${id}`, data),
 delete: (id) => axios.delete(`/support-tickets/${id}`),
 addMessage: (id, formData) => axios.post(`/support-tickets/${id}/messages`, formData, {
   headers: { 'Content-Type': 'multipart/form-data' }
 }),
};

// ==================== Payment Notifications ====================

export const paymentNotificationsAPI = {
 getAll: (params) => axios.get('/payment-notifications', { params }),
 getById: (id) => axios.get(`/payment-notifications/${id}`),
 create: (formData) => axios.post('/payment-notifications', formData, {
   headers: { 'Content-Type': 'multipart/form-data' }
 }),
 approve: (id) => axios.post(`/payment-notifications/${id}/approve`),
 reject: (id, reason) => axios.post(`/payment-notifications/${id}/reject`, { reason }),
};