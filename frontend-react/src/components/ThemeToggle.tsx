interface ThemeToggleProps {
  darkMode: boolean
  onToggle: () => void
}

export function ThemeToggle({ darkMode, onToggle }: ThemeToggleProps): JSX.Element {
  return (
    <button
      type="button"
      onClick={onToggle}
      className="rounded-xl border border-slate-300 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-200"
    >
      {darkMode ? '☀️ Light' : '🌙 Dark'}
    </button>
  )
}
