@php
  $pncp = $record->observacoes;

  if (is_string($pncp)) {
      $pncp = json_decode($pncp, true) ?: [];
  }
@endphp

<div class="space-y-4">
    <div>
        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
            {{ $record->orgao ?? '-' }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $record->numero_controle_pncp ?? '-' }}
        </div>
    </div>

    <x-filament::section heading="Objeto da Licitação">
        <div class="text-sm text-gray-800 dark:text-gray-100 leading-relaxed">
            {{ $pncp['objetoCompra'] ?? $record->descricao ?? '-' }}
        </div>
    </x-filament::section>

    <x-filament::section heading="Dados PNCP (debug)">
        <pre class="text-xs overflow-auto">{{ json_encode($pncp, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    </x-filament::section>
</div>
<div class="space-y-6">
    {{-- Ações --}}
    <div class="flex flex-wrap gap-2">
        @if(!empty($editalUrl))
            <a
                href="{{ $editalUrl }}"
                target="_blank"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium bg-primary-600 text-white hover:bg-primary-700"
            >
                Ver edital no PNCP
                <span class="opacity-80">↗</span>
            </a>
        @endif

        @if(!empty($record->numero_controle_pncp))
            <div class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-semibold">Número PNCP:</span> {{ $record->numero_controle_pncp }}
            </div>
        @endif
    </div>

    {{-- Itens --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
            <div class="font-semibold">Itens</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ count($itens) }} item(ns) retornado(s)
            </div>
        </div>

        @if(empty($itens))
            <div class="p-4 text-sm text-gray-600 dark:text-gray-300">
                Nenhum item disponível (ou a API não retornou itens para esse idCompra).
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-950">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold">Item</th>
                            <th class="px-4 py-3 font-semibold">Descrição</th>
                            <th class="px-4 py-3 font-semibold">Qtd</th>
                            <th class="px-4 py-3 font-semibold">Unid</th>
                            <th class="px-4 py-3 font-semibold">Vlr Unit</th>
                            <th class="px-4 py-3 font-semibold">Vlr Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($itens as $i)
                            @php
                                $num = $i['numeroItem'] ?? $i['item'] ?? '-';
                                $desc = $i['descricao'] ?? $i['descricaoItem'] ?? $i['objeto'] ?? '-';
                                $qtd = $i['quantidade'] ?? $i['qtd'] ?? null;
                                $un = $i['unidade'] ?? $i['unidadeFornecimento'] ?? null;
                                $vu = $i['valorUnitarioEstimado'] ?? $i['valorUnitario'] ?? null;
                                $vt = $i['valorTotalEstimado'] ?? $i['valorTotal'] ?? null;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $num }}</td>
                                <td class="px-4 py-3">
                                    <div class="line-clamp-3 max-w-[620px]">{{ $desc }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $qtd ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $un ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(is_numeric($vu))
                                        R$ {{ number_format((float)$vu, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(is_numeric($vt))
                                        <span class="font-semibold text-emerald-600">
                                            R$ {{ number_format((float)$vt, 2, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Documentos / Editais --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
            <div class="font-semibold">Documentos / Arquivos</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ count($documentos) }} documento(s) encontrado(s)
            </div>
        </div>

        @if(empty($documentos))
            <div class="p-4 text-sm text-gray-600 dark:text-gray-300">
                Nenhum documento retornado pela API do PNCP. Use o botão “Ver edital no PNCP”.
            </div>
        @else
            <div class="p-4 grid gap-2">
                @foreach($documentos as $d)
                    <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-800 px-3 py-2">
                        <div class="min-w-0">
                            <div class="font-medium truncate">
                                {{ $d['titulo'] ?? 'Documento' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $d['tipo'] ?? 'Tipo não informado' }}
                                @if(!empty($d['sequencialDocumento']))
                                    • ID: {{ $d['sequencialDocumento'] }}
                                @endif
                            </div>
                        </div>

                        @if(!empty($d['download_url']))
                            <a
                                href="{{ $d['download_url'] }}"
                                target="_blank"
                                class="shrink-0 inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold bg-gray-900 text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                            >
                                Baixar
                                <span class="opacity-80">↗</span>
                            </a>
                        @else
                            <span class="text-xs text-gray-500">indisponível</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

