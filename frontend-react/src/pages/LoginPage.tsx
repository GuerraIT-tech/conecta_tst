import { useMemo, useState } from 'react'

import { authApi } from '../services/api'

export function LoginPage(): JSX.Element {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [message, setMessage] = useState('')

  const errors = useMemo(() => ({
    email: /\S+@\S+\.\S+/.test(email) ? '' : 'Digite um e-mail válido.',
    password: password.length >= 6 ? '' : 'A senha deve ter pelo menos 6 caracteres.',
  }), [email, password])

  const isValid = Object.values(errors).every((value) => !value)

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault()
    if (!isValid) {
      setMessage('Corrija os campos antes de continuar.')
      return
    }
    try {
      const response = await authApi.login({ email, password })
      localStorage.setItem('access_token', response.data.access_token)
      setMessage('Login realizado com sucesso.')
    } catch {
      setMessage('Não foi possível autenticar agora. Verifique a API Flask.')
    }
  }

  return (
    <section className="mx-auto max-w-xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 className="text-3xl font-bold">Login</h2>
      <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Acesse a plataforma com segurança.</p>
      <form className="mt-6 space-y-4" onSubmit={handleSubmit}>
        <div>
          <input className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" placeholder="E-mail" value={email} onChange={(e) => setEmail(e.target.value)} />
          {errors.email && <p className="mt-2 text-sm text-rose-500">{errors.email}</p>}
        </div>
        <div>
          <input className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" type="password" placeholder="Senha" value={password} onChange={(e) => setPassword(e.target.value)} />
          {errors.password && <p className="mt-2 text-sm text-rose-500">{errors.password}</p>}
        </div>
        <button className="w-full rounded-xl bg-slate-900 px-4 py-3 font-semibold text-white dark:bg-sky-500 dark:text-slate-950" type="submit">Entrar</button>
      </form>
      {message && <p className="mt-4 text-sm text-slate-600 dark:text-slate-300">{message}</p>}
    </section>
  )
}
