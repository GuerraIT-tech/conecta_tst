import { useMemo, useState } from 'react'

export function ContactPage(): JSX.Element {
  const [form, setForm] = useState({ name: '', email: '', company: '', message: '' })
  const [submitted, setSubmitted] = useState(false)

  const errors = useMemo(() => ({
    name: form.name.trim() ? '' : 'Informe seu nome.',
    email: /\S+@\S+\.\S+/.test(form.email) ? '' : 'Informe um e-mail válido.',
    company: form.company.trim() ? '' : 'Informe a empresa.',
    message: form.message.trim().length >= 10 ? '' : 'Descreva a necessidade com pelo menos 10 caracteres.',
  }), [form])

  const isValid = Object.values(errors).every((value) => !value)

  return (
    <section className="mx-auto max-w-4xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 className="text-3xl font-bold">Contato Comercial</h2>
      <div className="mt-6 grid gap-4 md:grid-cols-2">
        {[
          { key: 'name', placeholder: 'Nome' },
          { key: 'email', placeholder: 'E-mail' },
          { key: 'company', placeholder: 'Empresa' },
        ].map((field) => (
          <div key={field.key}>
            <input
              className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
              placeholder={field.placeholder}
              value={form[field.key as keyof typeof form]}
              onChange={(event) => setForm((current) => ({ ...current, [field.key]: event.target.value }))}
            />
            {errors[field.key as keyof typeof errors] && <p className="mt-2 text-sm text-rose-500">{errors[field.key as keyof typeof errors]}</p>}
          </div>
        ))}
        <div className="md:col-span-2">
          <textarea
            className="min-h-40 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
            placeholder="Mensagem"
            value={form.message}
            onChange={(event) => setForm((current) => ({ ...current, message: event.target.value }))}
          />
          {errors.message && <p className="mt-2 text-sm text-rose-500">{errors.message}</p>}
        </div>
      </div>
      <button
        type="button"
        disabled={!isValid}
        onClick={() => setSubmitted(true)}
        className="mt-6 rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40 dark:bg-sky-500 dark:text-slate-950"
      >
        Enviar contato
      </button>
      {submitted && isValid && <p className="mt-4 text-sm text-emerald-500">Mensagem validada e pronta para integração com CRM.</p>}
    </section>
  )
}
