import { useEffect, useState } from 'react'

import { BarChart } from '../components/BarChart'
import { MetricCard } from '../components/MetricCard'
import { reportApi } from '../services/api'
import type { DashboardReport } from '../types/models'

const fallbackReport: DashboardReport = {
  kpis: { total_clients: 0, active_bids: 0, total_users: 0, radar_items: 0 },
  latest_clients: [],
  latest_bids: [],
  charts: {
    revenue_projection: { labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'], values: [0, 0, 0, 0, 0, 0] },
    pipeline_by_status: { labels: ['Novas', 'Em análise', 'Propostas', 'Ganhas'], values: [0, 0, 0, 0] },
  },
}

export function ReportsPage(): JSX.Element {
  const [report, setReport] = useState<DashboardReport>(fallbackReport)

  useEffect(() => {
    reportApi.dashboard()
      .then((response) => setReport(response))
      .catch(() => setReport(fallbackReport))
  }, [])

  return (
    <div className="space-y-6">
      <section className="grid gap-4 md:grid-cols-4">
        <MetricCard label="Clientes" value={report.kpis.total_clients} />
        <MetricCard label="Licitações ativas" value={report.kpis.active_bids} />
        <MetricCard label="Usuários" value={report.kpis.total_users} />
        <MetricCard label="Itens no radar" value={report.kpis.radar_items} />
      </section>

      <section className="grid gap-6 lg:grid-cols-2">
        <BarChart title="Projeção de Receita" series={report.charts.revenue_projection} />
        <BarChart title="Pipeline por Status" series={report.charts.pipeline_by_status} />
      </section>

      <section className="grid gap-6 lg:grid-cols-2">
        <article className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <h2 className="text-2xl font-bold">Últimos clientes</h2>
          <div className="mt-6 space-y-3">
            {report.latest_clients.map((client) => (
              <div key={client.id} className="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                <p className="font-semibold">{client.corporate_name}</p>
                <p className="text-sm text-slate-600 dark:text-slate-400">{client.city} / {client.state}</p>
              </div>
            ))}
          </div>
        </article>

        <article className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <h2 className="text-2xl font-bold">Últimas licitações</h2>
          <div className="mt-6 space-y-3">
            {report.latest_bids.map((bid) => (
              <div key={bid.id} className="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                <p className="font-semibold">{bid.bidding_number}</p>
                <p className="text-sm text-slate-600 dark:text-slate-400">{bid.bidding_modality}</p>
                <p className="text-sm text-slate-600 dark:text-slate-400">{bid.requesting_agency}</p>
              </div>
            ))}
          </div>
        </article>
      </section>
    </div>
  )
}
