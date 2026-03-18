import type { ChartSeries } from '../types/models'

interface BarChartProps {
  title: string
  series: ChartSeries
}

export function BarChart({ title, series }: BarChartProps): JSX.Element {
  const maxValue = Math.max(...series.values, 1)

  return (
    <section className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h3 className="text-lg font-semibold text-slate-900 dark:text-white">{title}</h3>
      <div className="mt-6 flex h-64 items-end gap-4">
        {series.values.map((value, index) => (
          <div key={`${series.labels[index]}-${value}`} className="flex flex-1 flex-col items-center gap-3">
            <div className="w-full rounded-t-2xl bg-gradient-to-t from-sky-500 to-cyan-300" style={{ height: `${(value / maxValue) * 100}%` }} />
            <span className="text-xs text-slate-500 dark:text-slate-400">{series.labels[index]}</span>
          </div>
        ))}
      </div>
    </section>
  )
}
