export function AdminPage(): JSX.Element {
  return (
    <div className="grid gap-6 lg:grid-cols-3">
      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
        <h2 className="text-3xl font-bold">Painel Administrativo</h2>
        <p className="mt-3 text-slate-600 dark:text-slate-400">Gestão de tarefas, relatórios, indicadores operacionais e acompanhamento das equipes.</p>
        <div className="mt-6 grid gap-4 md:grid-cols-3">
          {['Tarefas', 'Assinaturas', 'Indicadores', 'Pendências', 'Equipe', 'Exportação PDF'].map((item) => (
            <div key={item} className="rounded-2xl border border-slate-200 p-5 dark:border-slate-800">{item}</div>
          ))}
        </div>
      </section>
      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h3 className="text-xl font-bold">Resumo executivo</h3>
        <ul className="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          <li>• Controle central de operação</li>
          <li>• Visão de produtividade</li>
          <li>• Indicadores por carteira</li>
          <li>• Exportação para PDF</li>
        </ul>
      </section>
    </div>
  )
}
