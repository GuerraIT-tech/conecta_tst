import { useMemo, useState } from 'react'

import { authApi } from '../services/api'

export function RegisterPage(): JSX.Element {
  const [form, setForm] = useState({ name: '', email: '', cnpj: '', password: '' })
  const [message, setMessage] = useState('')

  const errors = useMemo(() => ({
    name: form.name.trim() ? '' : 'Informe o nome.',
    email: /\S+@\S+\.\S+/.test(form.email) ? '' : 'Informe um e-mail válido.',
    cnpj: form.cnpj.replace(/\D/g, '').length >= 14 ? '' : 'Informe um CNPJ válido.',
    password: form.password.length >= 6 ? '' : 'A senha deve ter pelo menos 6 caracteres.',
  }), [form])

  const isValid = Object.values(errors).every((value) => !value)

  const updateField = (field: keyof typeof form, value: string): void => {
    setForm((current) => ({ ...current, [field]: value }))
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault()
    if (!isValid) {
      setMessage('Existem campos inválidos no cadastro.')
      return
    }
    try {
      await authApi.register(form)
      setMessage('Cadastro enviado com sucesso.')
    } catch {
      setMessage('Não foi possível cadastrar agora. Verifique a API Flask.')
    }
  }

  return (
    <section className="mx-auto max-w-2xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 className="text-3xl font-bold">Cadastro</h2>
      <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Crie sua conta para acessar a área do cliente e os fluxos operacionais.</p>
      <form className="mt-6 grid gap-4 md:grid-cols-2" onSubmit={handleSubmit}>
        {[
          { key: 'name', placeholder: 'Nome' },
          { key: 'email', placeholder: 'E-mail' },
          { key: 'cnpj', placeholder: 'CNPJ' },
          { key: 'password', placeholder: 'Senha', type: 'password' },
        ].map((field) => (
          <div key={field.key}>
            <input
              className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
              placeholder={field.placeholder}
              type={field.type ?? 'text'}
              value={form[field.key as keyof typeof form]}
              onChange={(e) => updateField(field.key as keyof typeof form, e.target.value)}
            />
            {errors[field.key as keyof typeof errors] && <p className="mt-2 text-sm text-rose-500">{errors[field.key as keyof typeof errors]}</p>}
          </div>
        ))}
        <button className="rounded-xl bg-sky-500 px-4 py-3 font-semibold text-slate-950 md:col-span-2" type="submit">Cadastrar</button>
      </form>
      {message && <p className="mt-4 text-sm text-slate-600 dark:text-slate-300">{message}</p>}
    </section>
  )
}
