interface MetricCardProps {
  label: string
  value: number
}

export function MetricCard({ label, value }: MetricCardProps): JSX.Element {
  return (
    <article className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p className="text-sm text-slate-500 dark:text-slate-400">{label}</p>
      <p className="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{value}</p>
    </article>
  )
}
