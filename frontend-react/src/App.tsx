import { useEffect, useState } from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'

import { Layout } from './components/Layout'
import { AdminPage } from './pages/AdminPage'
import { BidsPage } from './pages/BidsPage'
import { ClientAreaPage } from './pages/ClientAreaPage'
import { ClientsPage } from './pages/ClientsPage'
import { ContactPage } from './pages/ContactPage'
import { HomePage } from './pages/HomePage'
import { LoginPage } from './pages/LoginPage'
import { PlansPage } from './pages/PlansPage'
import { RegisterPage } from './pages/RegisterPage'
import { ReportsPage } from './pages/ReportsPage'

export function App(): JSX.Element {
  const [darkMode, setDarkMode] = useState(false)

  useEffect(() => {
    document.documentElement.classList.toggle('dark', darkMode)
  }, [darkMode])

  return (
    <BrowserRouter>
      <Layout darkMode={darkMode} onToggleTheme={() => setDarkMode((current) => !current)}>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/planos" element={<PlansPage />} />
          <Route path="/contato" element={<ContactPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/cadastro" element={<RegisterPage />} />
          <Route path="/clientes" element={<ClientsPage />} />
          <Route path="/licitacoes" element={<BidsPage />} />
          <Route path="/relatorios" element={<ReportsPage />} />
          <Route path="/cliente-area" element={<ClientAreaPage />} />
          <Route path="/admin" element={<AdminPage />} />
        </Routes>
      </Layout>
    </BrowserRouter>
  )
}
