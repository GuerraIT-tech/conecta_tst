@php
    $status = $record->status_pncp ?? 'abertos';

    $statusLabel = match ($status) {
        'abertos' => 'ABERTO',
        'fechando' => 'FECHANDO',
        'fechado' => 'FECHADO',
        default => 'STATUS',
    };

    $badgeColor = match ($status) {
        'abertos' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        'fechando' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
        'fechado' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-200',
    };

    $topBorder = match ($status) {
        'abertos' => 'border-t-4 border-t-green-500',
        'fechando' => 'border-t-4 border-t-amber-500',
        'fechado' => 'border-t-4 border-t-red-500',
        default => 'border-t-4 border-t-gray-400',
    };

    $numero = $record->numero_controle_pncp ?? '-';
    $orgao = $record->orgao ?? '-';
    $objeto = $record->descricao ?? $record->titulo ?? '-';

    $modalidade = optional($record->modality)->name ?? '-';
    $enc = $record->data_hora_encerramento ? $record->data_hora_encerramento->format('d/m/Y H:i') : '-';

    $valor = is_numeric($record->valor) && $record->valor > 0
        ? 'R$ ' . number_format((float) $record->valor, 2, ',', '.')
        : null;
@endphp

<div
    class="cursor-pointer rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10 {{ $topBorder }}"
    x-data
    x-on:click="$wire.mountTableAction('view', '{{ $record->getKey() }}')"
>
    <div class="flex items-start justify-between gap-3">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
            {{ $numero }}
        </div>

        <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-wide {{ $badgeColor }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="mt-3 text-base font-extrabold text-gray-900 dark:text-white">
        {{ \Illuminate\Support\Str::upper($orgao) }}
    </div>

    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
        {{ $objeto }}
    </div>

    <div class="mt-4 space-y-2 text-sm">
        <div class="flex justify-between border-t border-gray-100 pt-3 dark:border-white/10">
            <span class="font-semibold text-gray-700 dark:text-gray-300">Modalidade:</span>
            <span class="text-gray-900 dark:text-white">{{ $modalidade }}</span>
        </div>

        <div class="flex justify-between">
            <span class="font-semibold text-gray-700 dark:text-gray-300">Encerramento:</span>
            <span class="text-gray-900 dark:text-white">{{ $enc }}</span>
        </div>

        @if($valor)
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Valor Estimado:</span>
                <span class="font-extrabold text-green-600 dark:text-green-400">{{ $valor }}</span>
            </div>
        @endif
    </div>
</div>
