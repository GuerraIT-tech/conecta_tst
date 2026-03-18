export function PlansPage(): JSX.Element {
  return (
    <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 className="text-3xl font-bold">Planos e Assinaturas</h2>
      <p className="mt-3 max-w-3xl text-slate-600 dark:text-slate-400">
        Configure e comercialize planos de consultoria em licitações com diferenciação por escopo, suporte, análise jurídica e operação assistida.
      </p>
      <div className="mt-8 grid gap-4 md:grid-cols-3">
        {['Start', 'Growth', 'Enterprise'].map((tier, index) => (
          <article key={tier} className="rounded-2xl border border-slate-200 p-6 dark:border-slate-800">
            <p className="text-sm font-medium text-sky-500">{tier}</p>
            <p className="mt-2 text-3xl font-bold">{index === 2 ? 'Custom' : `R$ ${(index + 1) * 990}`}</p>
            <ul className="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-400">
              <li>• Gestão de assinaturas</li>
              <li>• Fluxo comercial</li>
              <li>• Acompanhamento de licitações</li>
              <li>• Relatórios e indicadores</li>
            </ul>
          </article>
        ))}
      </div>
    </section>
  )
}
