<x-filament::page>
    <div wire:init="loadPregoes" class="space-y-6">

        {{-- FILTROS APLICADOS (da tela anterior/config) --}}
        <x-filament::card>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="text-sm font-extrabold text-gray-900 dark:text-white">
                        Filtros aplicados no seu Radar
                    </div>

                    <div class="flex flex-wrap gap-2 text-xs">
                        {{-- Palavra-chave --}}
                        <span class="rounded-full bg-gray-50 dark:bg-white/5 px-3 py-1 font-bold text-gray-700 dark:text-gray-200">
                            Palavra-chave:
                            <span class="font-extrabold">
                                {{ !empty($prefKeyword) ? $prefKeyword : 'SEM (busca tudo)' }}
                            </span>
                        </span>

                        {{-- Regiões --}}
                        <span class="rounded-full bg-gray-50 dark:bg-white/5 px-3 py-1 font-bold text-gray-700 dark:text-gray-200">
                            Regiões:
                            <span class="font-extrabold">
                                {{ !empty($prefRegions) ? implode(', ', $prefRegions) : 'TODAS' }}
                            </span>
                        </span>

                        {{-- UFs permitidas (resultado final) --}}
                        <span class="rounded-full bg-primary-50 dark:bg-primary-950/30 px-3 py-1 font-extrabold text-primary-700 dark:text-primary-200">
                            UFs permitidas:
                            <span class="font-extrabold">
                                {{ !empty($prefAllowedUfs) ? implode(', ', $prefAllowedUfs) : 'TODAS' }}
                            </span>
                        </span>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Esses filtros vêm da configuração e já estão aplicados aos resultados abaixo.
                    </div>
                </div>

                {{-- ajuste a rota se seu panel não for "admin" --}}
                <a
                    href="{{ route('filament.admin.pages.radar-v2-config') }}"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold bg-primary-600 text-white hover:bg-primary-700"
                >
                    Alterar filtros
                </a>
            </div>
        </x-filament::card>

        {{-- ERRO --}}
        @if($errorMessage)
            <x-filament::section>
                <div class="fi-ta-text-item px-4 py-3 rounded-xl bg-danger-50 text-danger-700 dark:bg-danger-950/30 dark:text-danger-200">
                    {{ $errorMessage }}
                </div>
            </x-filament::section>
        @endif

        {{-- STATS --}}
        <x-filament::grid default="1" md="2" xl="4" class="gap-4">
            <x-filament::card>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">PREGÕES ABERTOS</div>
                <div class="mt-2 text-3xl font-extrabold text-success-600 dark:text-success-400">
                    {{ $this->stats['abertos'] ?? 0 }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">FECHANDO EM BREVE</div>
                <div class="mt-2 text-3xl font-extrabold text-warning-600 dark:text-warning-400">
                    {{ $this->stats['fechando'] ?? 0 }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">FECHADOS</div>
                <div class="mt-2 text-3xl font-extrabold text-danger-600 dark:text-danger-400">
                    {{ $this->stats['fechado'] ?? 0 }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">FAVORITOS</div>
                <div class="mt-2 text-3xl font-extrabold text-primary-600 dark:text-primary-400">
                    {{ is_array($savedIds ?? null) ? count($savedIds) : 0 }}
                </div>
            </x-filament::card>
        </x-filament::grid>

        {{-- TABS --}}
        <x-filament::card>
            <div class="flex flex-wrap gap-2">
                <x-filament::button type="button" wire:click="setTab('abertos')" :color="$activeTab === 'abertos' ? 'primary' : 'gray'">
                    Abertos
                </x-filament::button>

                <x-filament::button type="button" wire:click="setTab('fechando')" :color="$activeTab === 'fechando' ? 'primary' : 'gray'">
                    Fechando
                </x-filament::button>

                <x-filament::button type="button" wire:click="setTab('fechado')" :color="$activeTab === 'fechado' ? 'primary' : 'gray'">
                    Fechados
                </x-filament::button>

                <x-filament::button type="button" wire:click="setTab('favoritos')" :color="$activeTab === 'favoritos' ? 'primary' : 'gray'">
                    Favoritos
                </x-filament::button>
            </div>
        </x-filament::card>

        {{-- FILTROS (da tela, adicionais) --}}
        @php
            // UF options: se tiver permitido, só mostra permitido
            $ufOptions = !empty($prefAllowedUfs)
                ? collect($prefAllowedUfs)
                : collect($allPregoes ?? [])
                    ->pluck('unidadeOrgaoUfSigla')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();

            $modalidadeOptions = collect($allPregoes ?? [])
                ->pluck('modalidadeNome')
                ->filter()
                ->unique()
                ->sort()
                ->values();

            $ufLocked = !empty($prefAllowedUfs) && count($prefAllowedUfs) === 1;
        @endphp

        <x-filament::section heading="Filtros (dentro do seu Radar)">
            <div class="space-y-4">

                <x-filament::grid default="1" md="2" xl="6" class="gap-3">
                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">Busca</label>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="q"
                            placeholder="Nº controle, órgão, objeto, processo..."
                            class="w-full rounded-xl border-gray-200 dark:border-gray-800"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">UF</label>

                        @if($ufLocked)
                            <input
                                type="text"
                                value="{{ $prefAllowedUfs[0] }}"
                                disabled
                                class="w-full rounded-xl border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-white/5"
                            />
                        @else
                            <select wire:model.live="fUf" class="w-full rounded-xl border-gray-200 dark:border-gray-800">
                                <option value="">Todas</option>
                                @foreach($ufOptions as $uf)
                                    <option value="{{ $uf }}">{{ $uf }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">Modalidade</label>
                        <select wire:model.live="fModalidade" class="w-full rounded-xl border-gray-200 dark:border-gray-800">
                            <option value="">Todas</option>
                            @foreach($modalidadeOptions as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">SRP</label>
                        <select wire:model.live="fSrp" class="w-full rounded-xl border-gray-200 dark:border-gray-800">
                            <option value="">Todos</option>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                </x-filament::grid>

                <x-filament::grid default="1" md="2" xl="6" class="gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">Valor mín.</label>
                        <input type="number" step="0.01" wire:model.live.debounce.400ms="fValorMin" placeholder="0"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-800" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">Valor máx.</label>
                        <input type="number" step="0.01" wire:model.live.debounce.400ms="fValorMax" placeholder="999999"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-800" />
                    </div>

                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">Encerramento (de)</label>
                        <input type="date" wire:model.live="fEncFrom" class="w-full rounded-xl border-gray-200 dark:border-gray-800" />
                    </div>

                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-xs font-bold text-gray-600 dark:text-gray-300">Encerramento (até)</label>
                        <input type="date" wire:model.live="fEncTo" class="w-full rounded-xl border-gray-200 dark:border-gray-800" />
                    </div>

                    <div class="xl:col-span-6 flex justify-end">
                        <x-filament::button type="button" color="gray" wire:click="clearFilters">
                            Limpar filtros
                        </x-filament::button>
                    </div>
                </x-filament::grid>

            </div>
        </x-filament::section>

        {{-- LOADING --}}
        <div wire:loading>
            <x-filament::card>
                <div class="py-8 text-center text-gray-600 dark:text-gray-200">
                    Carregando radar...
                </div>
            </x-filament::card>
        </div>

        {{-- LISTA --}}
        <div wire:loading.remove>
            @if(empty($allPregoes))
                <x-filament::card>
                    <div class="py-10 text-center text-gray-600 dark:text-gray-200">
                        Nenhum resultado encontrado ainda para o seu Radar.
                    </div>
                </x-filament::card>
            @else
                @if(empty($this->filteredPregoes))
                    <x-filament::card>
                        <div class="py-10 text-center text-gray-600 dark:text-gray-200">
                            Nenhum pregão encontrado com esses filtros.
                        </div>
                    </x-filament::card>
                @else
                    <x-filament::grid default="1" md="2" xl="3" class="gap-4">
                        @foreach($this->filteredPregoes as $p)
                            @php
                                $id = $p['idCompra'] ?? '';
                                $status = $p['status'] ?? 'abertos';

                                $accent = match($status) {
                                    'abertos' => 'border-l-success-500',
                                    'fechando' => 'border-l-warning-500',
                                    'fechado' => 'border-l-danger-500',
                                    default => 'border-l-gray-400',
                                };

                                $badge = match($status) {
                                    'abertos' => 'bg-success-50 text-success-700 dark:bg-success-950/30 dark:text-success-200',
                                    'fechando' => 'bg-warning-50 text-warning-700 dark:bg-warning-950/30 dark:text-warning-200',
                                    'fechado' => 'bg-danger-50 text-danger-700 dark:bg-danger-950/30 dark:text-danger-200',
                                    default => 'bg-gray-50 text-gray-700 dark:bg-gray-900/40 dark:text-gray-200',
                                };

                                $valor = (float) ($p['valorTotalEstimado'] ?? 0);
                                $enc = $p['dataEncerramentoPropostaPncp'] ?? null;
                                $saved = !empty($p['_saved']);
                            @endphp

                            <div
                                wire:key="prego-{{ $id }}"
                                class="rounded-2xl border-l-4 {{ $accent }} bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 shadow-sm hover:shadow-md transition"
                            >
                                <button
                                    type="button"
                                    wire:click="openDetails('{{ $id }}')"
                                    class="w-full text-left p-5"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 truncate">
                                                {{ $p['numeroControlePNCP'] ?? '-' }}
                                            </div>

                                            <div class="mt-1 text-base font-extrabold text-gray-900 dark:text-white truncate">
                                                {{ $p['orgaoEntidadeRazaoSocial'] ?? '-' }}
                                            </div>
                                        </div>

                                        <span class="shrink-0 inline-flex items-center rounded-full px-3 py-1 text-[11px] font-extrabold {{ $badge }}">
                                            {{ $p['statusLabel'] ?? 'STATUS' }}
                                        </span>
                                    </div>

                                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                        {{ $p['objetoCompra'] ?? '-' }}
                                    </div>

                                    <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                        <span class="rounded-full bg-gray-50 dark:bg-white/5 px-3 py-1 font-bold text-gray-700 dark:text-gray-200">
                                            {{ $p['modalidadeNome'] ?? '-' }}
                                        </span>

                                        <span class="rounded-full bg-gray-50 dark:bg-white/5 px-3 py-1 font-bold text-gray-700 dark:text-gray-200">
                                            {{ ($p['unidadeOrgaoMunicipioNome'] ?? '-') }} - {{ ($p['unidadeOrgaoUfSigla'] ?? '-') }}
                                        </span>

                                        <span class="rounded-full bg-gray-50 dark:bg-white/5 px-3 py-1 font-bold text-gray-700 dark:text-gray-200">
                                            Enc.: {{ $enc ? \Carbon\Carbon::parse($enc)->format('d/m/Y') : '-' }}
                                        </span>

                                        @if($valor > 0)
                                            <span class="rounded-full bg-success-50 dark:bg-success-950/30 px-3 py-1 font-extrabold text-success-700 dark:text-success-200">
                                                R$ {{ number_format($valor, 2, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </button>

                                <div class="flex items-center justify-between px-5 pb-4">
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400">
                                        Ver detalhes →
                                    </span>

                                    <button
                                        type="button"
                                        wire:click.stop="toggleSave('{{ $id }}')"
                                        class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold ring-1 ring-gray-200 dark:ring-gray-800 hover:bg-gray-50 dark:hover:bg-white/5"
                                        title="{{ $saved ? 'Remover dos favoritos' : 'Salvar nos favoritos' }}"
                                    >
                                        <x-filament::icon :icon="$saved ? 'heroicon-s-star' : 'heroicon-o-star'"
                                                         class="h-4 w-4 {{ $saved ? 'text-warning-500' : 'text-gray-400' }}" />
                                        {{ $saved ? 'Salvo' : 'Salvar' }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </x-filament::grid>
                @endif
            @endif
        </div>

        {{-- MODAL (mantive do seu modelo atual) --}}
        <x-filament::modal id="radarV2Modal" width="7xl" alignment="center">
            @php $p = $selectedPregao ?? []; @endphp

            <x-slot name="header">
                <div class="space-y-2">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-xl font-extrabold text-gray-900 dark:text-white truncate">
                                {{ $p['orgaoEntidadeRazaoSocial'] ?? 'Detalhes do Pregão' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $p['numeroControlePNCP'] ?? '' }}
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 justify-end">
                            @if(!empty($pregaoUrl))
                                <a
                                    href="{{ $pregaoUrl }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold bg-gray-900 text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                                >
                                    Abrir contratos <span class="opacity-80">↗</span>
                                </a>
                            @endif

                            <div class="flex flex-wrap gap-2">
                                @if(!empty($editalUrl))
                                    <a
                                        href="{{ $editalUrl }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold bg-primary-600 text-white hover:bg-primary-700"
                                    >
                                        Ver edital <span class="opacity-80">↗</span>
                                    </a>
                                @endif

                                @if(!empty($editalDownloadUrl))
                                    <a
                                        href="{{ $editalDownloadUrl }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold bg-gray-900 text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                                    >
                                        Baixar edital <span class="opacity-80">⬇</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>

            {{-- O restante do modal você pode manter exatamente como já está no seu arquivo atual --}}
            <div class="space-y-4">
                <x-filament::section heading="Objeto">
                    <x-filament::card>
                        {{ $p['objetoCompra'] ?? '-' }}
                    </x-filament::card>
                </x-filament::section>

                <x-filament::section heading="Documentos / Arquivos">
                    @if(empty($selectedDocumentos))
                        <x-filament::card>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                Nenhum documento retornado pela API.
                            </div>
                        </x-filament::card>
                    @else
                        <div class="grid gap-2">
                            @foreach($selectedDocumentos as $d)
                                @php
                                    $titulo = $d['titulo'] ?? $d['nome'] ?? $d['descricao'] ?? 'Documento';
                                    $tipo = $d['tipo'] ?? $d['categoria'] ?? 'Tipo não informado';
                                    $url = $d['download_url'] ?? $d['url'] ?? null;
                                @endphp

                                <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 dark:border-gray-800 px-3 py-2 bg-white dark:bg-gray-900">
                                    <div class="min-w-0">
                                        <div class="font-extrabold truncate text-gray-900 dark:text-white">
                                            {{ $titulo }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $tipo }}
                                        </div>
                                    </div>

                                    @if(!empty($url))
                                        <a
                                            href="{{ $url }}"
                                            target="_blank"
                                            class="shrink-0 inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold bg-gray-900 text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                                        >
                                            Abrir <span class="opacity-80">↗</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-500">indisponível</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-filament::button color="gray" wire:click="closeModal">
                        Fechar
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>

    </div>
</x-filament::page>
