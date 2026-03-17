<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $modelLabel = 'Documento';
    protected static ?string $pluralModelLabel = 'Documentos';
    protected static ?string $slug = 'documentos';
    protected static ?string $navigationGroup = 'Fundação';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Documento')
                ->schema([
                    Select::make('tipo_documento')
                        ->label('Tipo de Documento')
                        ->required()
                        ->options([
                            'Habilitação Jurídica' => [
                                'cartao_cnpj' => 'Cartão do CNPJ',
                                'contrato_social_consolidacao' => 'Contrato Social e Consolidação',
                                'rg_socio' => 'Documento de Identidade do Sócio Administrador',
                                'procuracao_representante' => 'Procuração da empresa para o Representante Legal',
                                'certificado_simplificada_porte' => 'Certificado Simplificada Porte da Empresa',
                            ],
                            'Habilitação Fiscal' => [
                                'cnd_federal' => 'Certidão Negativa de Débitos Federais (CND)',
                                'cnd_estadual' => 'Certidão Negativa de Débitos Estaduais (CND-UF)',
                                'cnd_municipal' => 'Certidão Negativa de Débitos Municipais (CND-Município)',
                                'crf_fgts' => 'Certidão de Regularidade do FGTS (CRF)',
                                'cndt_trabalhista' => 'Certidão Negativa de Débitos Trabalhistas (CNDT)',
                                'declaracao_simples_nacional' => 'Declaração de Optante pelo Simples Nacional',
                                'outros_certificados_fiscais' => 'Outros Certificados de Regularidade Fiscal',
                            ],
                            'Habilitação Econômica-Financeira' => [
                                'balanco_patrimonial' => 'Balanço Patrimonial e Demonstrações Contábeis',
                                'certidao_falencia_concordata' => 'Certidão Negativa de Falência e Concordata',
                                'declaracao_faturamento' => 'Declaração de Faturamento',
                                'outros_doc_financeiros' => 'Outros documentos que comprovem boa situação financeira',
                            ],
                            'Atestado de Capacidade Técnica' => [
                                'atestados_capacidade_tecnica' => 'Atestados de Capacidade Técnica',
                                'art_responsabilidade_tecnica' => 'ART (Anotação de Responsabilidade Técnica)',
                                'certificados_obra_servico' => 'Certificados de Obra/Serviço Concluído',
                            ],
                            'Certificados de Qualidade' => [
                                'iso' => 'Certificações ISO (9001, 14001, 45001)',
                                'cert_ambientais' => 'Certificações Ambientais',
                                'registro_federacao_conselho' => 'Registro na Federação ou Conselho Patronal',
                                'outras_certificacoes' => 'Outras certificações de gestão ou qualidade',
                            ],
                            'Certificados dos Profissionais' => [
                                'curriculo_profissional' => 'Currículos (CVs) dos profissionais-chave',
                                'diplomas_certificados_curso' => 'Diplomas e Certificados de Curso',
                                'registro_categoria_profissional' => 'Registro na Categoria/Conselho Profissional',
                                'outros_certificados_profissionais' => 'Outros certificados de qualificação profissional',
                            ],
                            'Declarações' => [
                                'declaracao_inexistencia_fatos_impeditivos' => 'Declaração de Inexistência de Fatos Impeditivos',
                                'declaracao_cumprimento_art7' => 'Declaração de Cumprimento do Art. 7º, Inciso XXXIII da CF',
                                'declaracao_microempresa' => 'Declaração de Microempresa/Empresa de Pequeno Porte',
                                'outras_declaracoes' => 'Outras declarações específicas de licitações',
                            ],
                            'Outros Documentos' => [
                                'procuracao_conectar' => 'Procuração da empresa para a Conectar',
                                'papel_timbrado' => 'Papel Timbrado da empresa',
                                'documentos_adicionais' => 'Documentos Adicionais não categorizados',
                            ],
                        ])
                        ->searchable(),
                    Forms\Components\Grid::make(2) // 2 colunas
                    ->schema([
                        Forms\Components\DatePicker::make('data_emissao')
                            ->label('Data de Emissão')
                            ->native(false) // usa o datepicker do Filament
                            ->required(),

                        Forms\Components\DatePicker::make('data_validade')
                            ->label('Data de Validade')
                            ->native(false)
                            ->required(),
                    ]),
                    TextInput::make('orgao_emissor')
                        ->label('Órgão Emissor / Descrição')
                        ->placeholder('Ex: Receita Federal, Junta Comercial, etc...')
                        ->maxLength(255),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'valido' => 'Válido',
                            'expirado' => 'Expirado',
                            'pendente_revisao' => 'Pendente de revisão',
                            'nao_conforme' => 'Não Conforme',
                        ])
                        ->default('pendente')
                        ->required(),
                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('arquivo')
                        ->label('Arquivo do Documento')
                        ->disk('public') // ou outro disco configurado no config/filesystems.php
                        ->directory('documentos') // pasta onde vai salvar
                        ->preserveFilenames() // mantém o nome original
                        ->maxSize(5120) // tamanho máximo em KB (aqui 5MB)
                        ->acceptedFileTypes(['application/pdf', 'image/*']) // PDF ou imagens
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /*Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),*/
                Tables\Columns\TextColumn::make('tipo_documento')
                ->label('Tipo de Documento')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($state) {
                    $map = [
                        'cartao_cnpj' => 'Cartão do CNPJ',
                        'contrato_social_consolidacao' => 'Contrato Social e Consolidação',
                        'doc_identidade_socio' => 'Documento de Identidade do Sócio Administrador',
                        'procuracao_representante' => 'Procuração da empresa para o Representante Legal',
                        'certificado_simplificada_porte' => 'Certificado Simplificada Porte da Empresa',
                        'cnd_federal' => 'Certidão Negativa de Débitos Federais (CND)',
                        'cnd_estadual' => 'Certidão Negativa de Débitos Estaduais (CND-UF)',
                        'cnd_municipal' => 'Certidão Negativa de Débitos Municipais (CND-Município)',
                        'crf_fgts' => 'Certidão de Regularidade do FGTS (CRF)',
                        'cndt_trabalhista' => 'Certidão Negativa de Débitos Trabalhistas (CNDT)',
                        'declaracao_simples_nacional' => 'Declaração de Optante pelo Simples Nacional',
                        'outros_certificados_fiscais' => 'Outros Certificados de Regularidade Fiscal',
                        'balanco_patrimonial' => 'Balanço Patrimonial e Demonstrações Contábeis',
                        'certidao_falencia_concordata' => 'Certidão Negativa de Falência e Concordata',
                        'declaracao_faturamento' => 'Declaração de Faturamento',
                        'outros_doc_financeiros' => 'Outros documentos que comprovem boa situação financeira',
                        'atestados_capacidade_tecnica' => 'Atestados de Capacidade Técnica',
                        'art_responsabilidade_tecnica' => 'ART (Anotação de Responsabilidade Técnica)',
                        'certificados_obra_servico' => 'Certificados de Obra/Serviço Concluído',
                        'cert_iso' => 'Certificações ISO (9001, 14001, 45001)',
                        'cert_ambientais' => 'Certificações Ambientais',
                        'registro_federacao_conselho' => 'Registro na Federação ou Conselho Patronal',
                        'outras_certificacoes' => 'Outras certificações de gestão ou qualidade',
                        'curriculo_profissional' => 'Currículos (CVs) dos profissionais-chave',
                        'diplomas_certificados_curso' => 'Diplomas e Certificados de Curso',
                        'registro_categoria_profissional' => 'Registro na Categoria/Conselho Profissional',
                        'outros_certificados_profissionais' => 'Outros certificados de qualificação profissional',
                        'declaracao_inexistencia_fatos_impeditivos' => 'Declaração de Inexistência de Fatos Impeditivos',
                        'declaracao_cumprimento_art7' => 'Declaração de Cumprimento do Art. 7º, Inciso XXXIII da CF',
                        'declaracao_microempresa' => 'Declaração de Microempresa/Empresa de Pequeno Porte',
                        'outras_declaracoes' => 'Outras declarações específicas de licitações',
                        'procuracao_conectar' => 'Procuração da empresa para a Conectar',
                        'papel_timbrado' => 'Papel Timbrado da empresa',
                        'documentos_adicionais' => 'Documentos Adicionais não categorizados',
                    ];

                    return $map[$state] ?? $state;
                }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'valido' => 'Válido',
                            'expirado' => 'Expirado',
                            'pendente_revisao' => 'Pendente de revisão',
                            'nao_conforme' => 'Não conforme',
                        ];

                        return $map[$state] ?? $state;
                    })
                    ->badge() // exibe com cor de destaque
                    ->colors([
                        'success' => 'Válido',
                        'danger' => 'Expirado',
                        'warning' => 'Pendente de revisão',
                        'secondary' => 'Não conforme',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_emissao')
                    ->label('Emissão')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_validade')
                    ->label('Validade')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('orgao_emissor')
                    ->label('Órgão Emissor / Descrição')
                    ->sortable()
                    ->wrap(), // quebra texto se for grande

                Tables\Columns\TextColumn::make('observacoes')
                    ->label('Observações')
                    ->limit(30) // mostra só um resumo
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocuments::route('/'),
        ];
    }
}
