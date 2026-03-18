export function ClientAreaPage(): JSX.Element {
  return (
    <div className="grid gap-6 lg:grid-cols-3">
      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
        <h2 className="text-3xl font-bold">Área do Cliente</h2>
        <p className="mt-3 text-slate-600 dark:text-slate-400">Espaço para upload de documentos, checklist operacional e acompanhamento de licitações.</p>
        <div className="mt-6 grid gap-4 md:grid-cols-2">
          {['Upload de documentos', 'Checklist de pendências', 'Andamento das licitações', 'Solicitações e suporte'].map((item) => (
            <div key={item} className="rounded-2xl border border-dashed border-slate-300 p-5 text-sm dark:border-slate-700">{item}</div>
          ))}
        </div>
      </section>
      <section className="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h3 className="text-xl font-bold">IA Jurídica</h3>
        <p className="mt-3 text-sm text-slate-600 dark:text-slate-400">Painel previsto para análise documental, identificação de pendências e recomendações automáticas.</p>
      </section>
    </div>
  )
}
