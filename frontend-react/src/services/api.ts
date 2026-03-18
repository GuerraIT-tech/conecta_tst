import axios from 'axios'

import type { Bid, Company, DashboardReport } from '../types/models'

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:5000/api',
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const authApi = {
  login: (payload: { email: string; password: string }) => api.post<{ access_token: string }>('/auth/login', payload),
  register: (payload: { name: string; email: string; password: string; cnpj?: string }) => api.post('/auth/register', payload),
}

export const companyApi = {
  list: async (): Promise<Company[]> => (await api.get<Company[]>('/companies')).data,
  create: async (payload: Partial<Company>): Promise<Company> => (await api.post<Company>('/companies', payload)).data,
  update: async (id: number, payload: Partial<Company>): Promise<Company> => (await api.put<Company>(`/companies/${id}`, payload)).data,
  remove: async (id: number): Promise<void> => { await api.delete(`/companies/${id}`) },
}

export const bidApi = {
  list: async (): Promise<Bid[]> => (await api.get<Bid[]>('/bids')).data,
  create: async (payload: Partial<Bid>): Promise<Bid> => (await api.post<Bid>('/bids', payload)).data,
  update: async (id: number, payload: Partial<Bid>): Promise<Bid> => (await api.put<Bid>(`/bids/${id}`, payload)).data,
  remove: async (id: number): Promise<void> => { await api.delete(`/bids/${id}`) },
}

export const reportApi = {
  dashboard: async (): Promise<DashboardReport> => (await api.get<DashboardReport>('/reports/dashboard')).data,
}
