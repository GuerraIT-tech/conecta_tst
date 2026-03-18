import { useEffect, useState } from 'react'

import { api } from '../services/api'
import type { Radar } from '../types/models'

export function HomePage(): JSX.Element {
  const [radars, setRadars] = useState<Radar[]>([])

  useEffect(() => {
    api.get<Radar[]>('/radars')
      .then((response) => setRadars(response.data))
      .catch(() => setRadars([]))
  }, [])

  return (
    <main className="mx-auto max-w-5xl p-8">
      <header className="mb-8 rounded-xl bg-slate-900 p-6 text-white">
        <h1 className="text-3xl font-bold">HCL Licitações - React + Flask</h1>
        <p className="mt-2 text-slate-300">Migração inicial da landing e consumo da API Flask desacoplada.</p>
      </header>

      <section className="rounded-xl bg-white p-6 shadow">
        <h2 className="mb-4 text-xl font-semibold">Radar de Oportunidades</h2>
        <ul className="space-y-3">
          {radars.map((radar) => (
            <li key={radar.id} className="rounded border border-slate-200 p-3">
              <p className="font-medium">{radar.titulo}</p>
              <p className="text-sm text-slate-600">Status: {radar.situacao}</p>
            </li>
          ))}
          {radars.length === 0 && <li className="text-slate-500">Nenhum radar carregado.</li>}
        </ul>
      </section>
    </main>
  )
}
