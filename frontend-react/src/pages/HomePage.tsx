import { Link } from 'react-router-dom'

const plans = [
  {
    name: 'Essencial',
    price: 'R$ 790/mês',
    description: 'Ideal para empresas iniciando em licitações com acompanhamento consultivo.',
  },
  {
    name: 'Performance',
    price: 'R$ 1.490/mês',
    description: 'Operação comercial com checklist, documentos, inteligência e monitoramento de oportunidades.',
  },
  {
    name: 'Enterprise',
    price: 'Sob consulta',
    description: 'Estrutura completa com painel administrativo, tarefas, relatórios em PDF e IA jurídica.',
  },
]

export function HomePage(): JSX.Element {
  return (
    <div className="space-y-10">
      <section className="grid gap-6 rounded-[2rem] bg-gradient-to-br from-slate-950 via-slate-900 to-sky-900 p-10 text-white lg:grid-cols-[1.2fr,0.8fr]">
        <div>
          <p className="text-sm uppercase tracking-[0.3em] text-sky-300">Website institucional + plataforma</p>
          <h2 className="mt-4 text-4xl font-bold leading-tight lg:text-5xl">
            Comercialização de planos de consultoria em licitações com experiência premium, intuitiva e profissional.
          </h2>
          <p className="mt-5 max-w-2xl text-lg text-slate-300">
            Um ecossistema completo com website institucional, gestão de planos e assinaturas, área do cliente,
            acompanhamento de licitações, upload documental, checklist, relatórios, inteligência jurídica e painel administrativo.
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <Link to="/planos" className="rounded-2xl bg-sky-400 px-5 py-3 font-semibold text-slate-950">Ver planos</Link>
            <Link to="/cadastro" className="rounded-2xl border border-white/20 px-5 py-3 font-semibold">Criar conta</Link>
          </div>
        </div>
        <div className="grid gap-4 rounded-[1.5rem] bg-white/10 p-6 backdrop-blur">
          {[
            'Upload de documentos com checklist operacional',
            'Gestão de clientes, licitações e relatórios',
            'IA jurídica para análise documental e pendências',
            'Painel administrativo com visão estratégica',
          ].map((item) => (
            <div key={item} className="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-200">{item}</div>
          ))}
        </div>
      </section>

      <section className="grid gap-6 md:grid-cols-3">
        {plans.map((plan) => (
          <article key={plan.name} className="rounded-[1.75rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p className="text-sm font-medium text-sky-500">Plano {plan.name}</p>
            <h3 className="mt-2 text-2xl font-bold">{plan.price}</h3>
            <p className="mt-4 text-sm text-slate-600 dark:text-slate-400">{plan.description}</p>
            <Link to="/contato" className="mt-6 inline-flex rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-sky-500 dark:text-slate-950">
              Solicitar proposta
            </Link>
          </article>
        ))}
      </section>
    </div>
  )
}
