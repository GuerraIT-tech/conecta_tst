import { useEffect, useMemo, useState } from 'react'

import { bidApi } from '../services/api'
import type { Bid } from '../types/models'

const initialForm: Partial<Bid> = {
  bidding_modality: 'Pregão Eletrônico',
  bidding_number: '',
  requesting_agency: '',
  registration_email: '',
  auctioneer_name: '',
}

export function BidsPage(): JSX.Element {
  const [bids, setBids] = useState<Bid[]>([])
  const [form, setForm] = useState<Partial<Bid>>(initialForm)
  const [message, setMessage] = useState('')
  const [editingId, setEditingId] = useState<number | null>(null)
  const [search, setSearch] = useState('')

  const errors = useMemo(() => ({
    bidding_modality: form.bidding_modality?.trim() ? '' : 'Selecione a modalidade.',
    bidding_number: form.bidding_number?.trim() ? '' : 'Informe o número da licitação.',
  }), [form])

  const isValid = Object.values(errors).every((value) => !value)

  const loadBids = async (): Promise<void> => {
    try {
      setBids(await bidApi.list())
    } catch {
      setBids([])
    }
  }

  useEffect(() => {
    void loadBids()
  }, [])

  const resetForm = (): void => {
    setForm(initialForm)
    setEditingId(null)
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault()
    if (!isValid) {
      setMessage('Corrija os campos obrigatórios.')
      return
    }
    try {
      if (editingId) {
        await bidApi.update(editingId, form)
        setMessage('Licitação atualizada com sucesso.')
      } else {
        await bidApi.create(form)
        setMessage('Licitação cadastrada com sucesso.')
      }
      resetForm()
      await loadBids()
    } catch {
      setMessage('Não foi possível salvar a licitação agora.')
    }
  }

  const filteredBids = bids.filter((bid) => {
    const term = search.toLowerCase()
    return [bid.bidding_number, bid.bidding_modality, bid.requesting_agency]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(term))
  })

  return (
    <div className="grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="flex items-center justify-between gap-4">
          <div>
            <h2 className="text-2xl font-bold">CRUD de licitações</h2>
            <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Gerencie oportunidades, credenciamento e responsáveis.</p>
          </div>
          {editingId && <button type="button" onClick={resetForm} className="rounded-xl border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Cancelar edição</button>}
        </div>

        <form className="mt-6 grid gap-4" onSubmit={handleSubmit}>
          <div>
            <select className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" value={String(form.bidding_modality ?? '')} onChange={(e) => setForm((current) => ({ ...current, bidding_modality: e.target.value }))}>
              <option>Pregão Eletrônico</option>
              <option>Concorrência Eletrônica</option>
              <option>Pregão Eletrônico Registro de Preços</option>
              <option>Dispensa de Licitação</option>
            </select>
            {errors.bidding_modality && <p className="mt-2 text-sm text-rose-500">{errors.bidding_modality}</p>}
          </div>
          <div>
            <input className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" placeholder="Número da licitação" value={String(form.bidding_number ?? '')} onChange={(e) => setForm((current) => ({ ...current, bidding_number: e.target.value }))} />
            {errors.bidding_number && <p className="mt-2 text-sm text-rose-500">{errors.bidding_number}</p>}
          </div>
          <input className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" placeholder="Órgão solicitante" value={String(form.requesting_agency ?? '')} onChange={(e) => setForm((current) => ({ ...current, requesting_agency: e.target.value }))} />
          <input className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" placeholder="E-mail de credenciamento" value={String(form.registration_email ?? '')} onChange={(e) => setForm((current) => ({ ...current, registration_email: e.target.value }))} />
          <input className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" placeholder="Pregoeiro" value={String(form.auctioneer_name ?? '')} onChange={(e) => setForm((current) => ({ ...current, auctioneer_name: e.target.value }))} />
          <button className="rounded-xl bg-slate-900 px-4 py-3 font-semibold text-white dark:bg-sky-500 dark:text-slate-950" type="submit">
            {editingId ? 'Atualizar licitação' : 'Salvar licitação'}
          </button>
        </form>
        {message && <p className="mt-4 text-sm text-slate-600 dark:text-slate-300">{message}</p>}
      </section>

      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <h2 className="text-2xl font-bold">Licitações cadastradas</h2>
          <input className="rounded-xl border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-950" placeholder="Buscar licitação..." value={search} onChange={(e) => setSearch(e.target.value)} />
        </div>
        <div className="mt-6 space-y-3">
          {filteredBids.map((bid) => (
            <article key={bid.id} className="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
              <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                  <h3 className="font-semibold">{bid.bidding_number}</h3>
                  <p className="text-sm text-slate-600 dark:text-slate-400">{bid.bidding_modality}</p>
                  <p className="text-sm text-slate-600 dark:text-slate-400">{bid.requesting_agency}</p>
                </div>
                <div className="flex gap-2">
                  <button type="button" onClick={() => { setEditingId(bid.id); setForm(bid) }} className="rounded-xl bg-amber-100 px-4 py-2 text-sm font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">Editar</button>
                  <button type="button" onClick={() => void (async () => { try { await bidApi.remove(bid.id); setMessage('Licitação removida com sucesso.'); await loadBids() } catch { setMessage('Não foi possível remover a licitação.') } })()} className="rounded-xl bg-rose-100 px-4 py-2 text-sm font-medium text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">Excluir</button>
                </div>
              </div>
            </article>
          ))}
          {filteredBids.length === 0 && <p className="text-sm text-slate-500">Nenhuma licitação encontrada.</p>}
        </div>
      </section>
    </div>
  )
}
