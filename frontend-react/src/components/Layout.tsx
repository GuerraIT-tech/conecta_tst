import type { ReactNode } from 'react'
import { NavLink } from 'react-router-dom'

import { ThemeToggle } from './ThemeToggle'

interface LayoutProps {
  children: ReactNode
  darkMode: boolean
  onToggleTheme: () => void
}

const menuItems = [
  { to: '/', label: 'Home' },
  { to: '/planos', label: 'Planos' },
  { to: '/contato', label: 'Contato' },
  { to: '/login', label: 'Login' },
  { to: '/cadastro', label: 'Cadastro' },
  { to: '/clientes', label: 'Clientes' },
  { to: '/licitacoes', label: 'Licitações' },
  { to: '/relatorios', label: 'Relatórios' },
  { to: '/cliente-area', label: 'Área do Cliente' },
  { to: '/admin', label: 'Admin' },
]

export function Layout({ children, darkMode, onToggleTheme }: LayoutProps): JSX.Element {
  return (
    <div className="min-h-screen bg-slate-100 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
      <header className="sticky top-0 z-20 border-b border-slate-200/70 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
        <div className="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-4 xl:flex-row xl:items-center xl:justify-between">
          <div>
            <h1 className="text-xl font-bold">Conecta Licitações</h1>
            <p className="text-sm text-slate-500 dark:text-slate-400">Plataforma comercial, operação do cliente e gestão administrativa.</p>
          </div>
          <div className="flex flex-col gap-3 xl:items-end">
            <nav className="flex flex-wrap gap-2">
              {menuItems.map((item) => (
                <NavLink
                  key={item.to}
                  to={item.to}
                  className={({ isActive }) =>
                    `rounded-xl px-4 py-2 text-sm font-medium transition ${isActive ? 'bg-slate-900 text-white dark:bg-sky-500 dark:text-slate-950' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'}`
                  }
                >
                  {item.label}
                </NavLink>
              ))}
            </nav>
            <ThemeToggle darkMode={darkMode} onToggle={onToggleTheme} />
          </div>
        </div>
      </header>
      <main className="mx-auto max-w-7xl px-6 py-8">{children}</main>
    </div>
  )
}
