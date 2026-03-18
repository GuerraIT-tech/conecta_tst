import { useEffect, useMemo, useState } from 'react'

import { companyApi } from '../services/api'
import type { Company } from '../types/models'

const initialForm: Partial<Company> = {
  corporate_name: '',
  trade_name: '',
  cnpj: '',
  address: '',
  number: '',
  district: '',
  city: '',
  state: '',
  zip_code: '',
  phone: '',
  email: '',
}

export function ClientsPage(): JSX.Element {
  const [companies, setCompanies] = useState<Company[]>([])
  const [form, setForm] = useState<Partial<Company>>(initialForm)
  const [message, setMessage] = useState('')
  const [editingId, setEditingId] = useState<number | null>(null)
  const [search, setSearch] = useState('')

  const errors = useMemo(() => ({
    corporate_name: form.corporate_name?.trim() ? '' : 'Informe a razão social.',
    email: /\S+@\S+\.\S+/.test(form.email ?? '') ? '' : 'Informe um e-mail válido.',
    city: form.city?.trim() ? '' : 'Informe a cidade.',
    state: form.state?.trim() ? '' : 'Informe o estado.',
  }), [form])

  const isValid = Object.values(errors).every((value) => !value)

  const loadCompanies = async (): Promise<void> => {
    try {
      setCompanies(await companyApi.list())
    } catch {
      setCompanies([])
    }
  }

  useEffect(() => {
    void loadCompanies()
  }, [])

  const resetForm = (): void => {
    setForm(initialForm)
    setEditingId(null)
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault()
    if (!isValid) {
      setMessage('Existem campos obrigatórios inválidos.')
      return
    }

    try {
      if (editingId) {
        await companyApi.update(editingId, form)
        setMessage('Cliente atualizado com sucesso.')
      } else {
        await companyApi.create(form)
        setMessage('Cliente cadastrado com sucesso.')
      }
      resetForm()
      await loadCompanies()
    } catch {
      setMessage('Não foi possível salvar o cliente agora.')
    }
  }

  const handleEdit = (company: Company): void => {
    setEditingId(company.id)
    setForm(company)
  }

  const handleDelete = async (id: number): Promise<void> => {
    try {
      await companyApi.remove(id)
      setMessage('Cliente removido com sucesso.')
      await loadCompanies()
    } catch {
      setMessage('Não foi possível remover o cliente.')
    }
  }

  const filteredCompanies = companies.filter((company) => {
    const term = search.toLowerCase()
    return [company.corporate_name, company.trade_name, company.cnpj, company.city, company.state]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(term))
  })

  return (
    <div className="grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="flex items-center justify-between gap-4">
          <div>
            <h2 className="text-2xl font-bold">CRUD de clientes</h2>
            <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Cadastre, edite e remova clientes do pipeline comercial.</p>
          </div>
          {editingId && <button type="button" onClick={resetForm} className="rounded-xl border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Cancelar edição</button>}
        </div>

        <form className="mt-6 grid gap-4 md:grid-cols-2" onSubmit={handleSubmit}>
          {[
            ['corporate_name', 'Razão social'],
            ['trade_name', 'Nome fantasia'],
            ['cnpj', 'CNPJ'],
            ['email', 'E-mail'],
            ['phone', 'Telefone'],
            ['zip_code', 'CEP'],
            ['address', 'Endereço'],
            ['number', 'Número'],
            ['district', 'Bairro'],
            ['city', 'Cidade'],
            ['state', 'Estado'],
          ].map(([key, label]) => (
            <div key={key}>
              <input
                className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
                placeholder={label}
                value={String(form[key as keyof Company] ?? '')}
                onChange={(e) => setForm((current) => ({ ...current, [key]: e.target.value }))}
              />
              {errors[key as keyof typeof errors] && <p className="mt-2 text-sm text-rose-500">{errors[key as keyof typeof errors]}</p>}
            </div>
          ))}
          <button className="rounded-xl bg-slate-900 px-4 py-3 font-semibold text-white md:col-span-2 dark:bg-sky-500 dark:text-slate-950" type="submit">
            {editingId ? 'Atualizar cliente' : 'Salvar cliente'}
          </button>
        </form>
        {message && <p className="mt-4 text-sm text-slate-600 dark:text-slate-300">{message}</p>}
      </section>

      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <h2 className="text-2xl font-bold">Clientes cadastrados</h2>
          <input
            className="rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
            placeholder="Buscar cliente..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>
        <div className="mt-6 space-y-3">
          {filteredCompanies.map((company) => (
            <article key={company.id} className="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
              <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                  <h3 className="font-semibold">{company.corporate_name}</h3>
                  <p className="text-sm text-slate-600 dark:text-slate-400">{company.city} / {company.state}</p>
                  <p className="text-sm text-slate-600 dark:text-slate-400">{company.cnpj}</p>
                </div>
                <div className="flex gap-2">
                  <button type="button" onClick={() => handleEdit(company)} className="rounded-xl bg-amber-100 px-4 py-2 text-sm font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">Editar</button>
                  <button type="button" onClick={() => void handleDelete(company.id)} className="rounded-xl bg-rose-100 px-4 py-2 text-sm font-medium text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">Excluir</button>
                </div>
              </div>
            </article>
          ))}
          {filteredCompanies.length === 0 && <p className="text-sm text-slate-500">Nenhum cliente encontrado.</p>}
        </div>
      </section>
    </div>
  )
}
