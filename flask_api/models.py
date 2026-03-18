from __future__ import annotations

from datetime import date, datetime
from decimal import Decimal
from typing import Any

from sqlalchemy import (
    BigInteger,
    Boolean,
    Date,
    DateTime,
    DECIMAL,
    Enum,
    ForeignKey,
    Index,
    Integer,
    JSON,
    PrimaryKeyConstraint,
    String,
    Text,
    UniqueConstraint,
    text,
)
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column, relationship


class Base(DeclarativeBase):
    pass


class User(Base):
    __tablename__ = "users"

    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    email: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)
    cnpj: Mapped[str | None] = mapped_column(String(18))
    email_verified_at: Mapped[datetime | None] = mapped_column(DateTime)
    password: Mapped[str] = mapped_column(String(255), nullable=False)
    remember_token: Mapped[str | None] = mapped_column(String(100))
    is_active: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("1"))
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    favorites: Mapped[list[Favorite]] = relationship(back_populates="user")
    favorite_bids: Mapped[list[Bid]] = relationship("Bid", secondary="favorites", back_populates="favorited_by")
    saved_pregoes: Mapped[list[SavedPregao]] = relationship(back_populates="user")
    radar_preference: Mapped[RadarPreference | None] = relationship(back_populates="user", uselist=False)
    radar_results: Mapped[list[RadarResult]] = relationship(back_populates="user")


class PasswordResetToken(Base):
    __tablename__ = "password_reset_tokens"

    email: Mapped[str] = mapped_column(String(255), primary_key=True)
    token: Mapped[str] = mapped_column(String(255), nullable=False)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)


class Session(Base):
    __tablename__ = "sessions"

    id: Mapped[str] = mapped_column(String(255), primary_key=True)
    user_id: Mapped[int | None] = mapped_column(ForeignKey("users.id"))
    ip_address: Mapped[str | None] = mapped_column(String(45))
    user_agent: Mapped[str | None] = mapped_column(Text)
    payload: Mapped[str] = mapped_column(Text, nullable=False)
    last_activity: Mapped[int] = mapped_column(Integer, nullable=False)


class Cache(Base):
    __tablename__ = "cache"

    key: Mapped[str] = mapped_column(String(255), primary_key=True)
    value: Mapped[str] = mapped_column(Text, nullable=False)
    expiration: Mapped[int] = mapped_column(Integer, nullable=False)


class CacheLock(Base):
    __tablename__ = "cache_locks"

    key: Mapped[str] = mapped_column(String(255), primary_key=True)
    owner: Mapped[str] = mapped_column(String(255), nullable=False)
    expiration: Mapped[int] = mapped_column(Integer, nullable=False)


class Job(Base):
    __tablename__ = "jobs"

    id: Mapped[int] = mapped_column(primary_key=True)
    queue: Mapped[str] = mapped_column(String(255), nullable=False)
    payload: Mapped[str] = mapped_column(Text, nullable=False)
    attempts: Mapped[int] = mapped_column(Integer, nullable=False)
    reserved_at: Mapped[int | None] = mapped_column(Integer)
    available_at: Mapped[int] = mapped_column(Integer, nullable=False)
    created_at: Mapped[int] = mapped_column(Integer, nullable=False)


class JobBatch(Base):
    __tablename__ = "job_batches"

    id: Mapped[str] = mapped_column(String(255), primary_key=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    total_jobs: Mapped[int] = mapped_column(Integer, nullable=False)
    pending_jobs: Mapped[int] = mapped_column(Integer, nullable=False)
    failed_jobs: Mapped[int] = mapped_column(Integer, nullable=False)
    failed_job_ids: Mapped[str] = mapped_column(Text, nullable=False)
    options: Mapped[str | None] = mapped_column(Text)
    cancelled_at: Mapped[int | None] = mapped_column(Integer)
    created_at: Mapped[int] = mapped_column(Integer, nullable=False)
    finished_at: Mapped[int | None] = mapped_column(Integer)


class FailedJob(Base):
    __tablename__ = "failed_jobs"

    id: Mapped[int] = mapped_column(primary_key=True)
    uuid: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)
    connection: Mapped[str] = mapped_column(Text, nullable=False)
    queue: Mapped[str] = mapped_column(Text, nullable=False)
    payload: Mapped[str] = mapped_column(Text, nullable=False)
    exception: Mapped[str] = mapped_column(Text, nullable=False)
    failed_at: Mapped[datetime] = mapped_column(DateTime, nullable=False)


class Permission(Base):
    __tablename__ = "permissions"
    __table_args__ = (UniqueConstraint("name", "guard_name", name="permissions_name_guard_name_unique"),)

    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    guard_name: Mapped[str] = mapped_column(String(255), nullable=False)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)


class Role(Base):
    __tablename__ = "roles"
    __table_args__ = (UniqueConstraint("name", "guard_name", name="roles_name_guard_name_unique"),)

    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    guard_name: Mapped[str] = mapped_column(String(255), nullable=False)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)


class ModelHasPermission(Base):
    __tablename__ = "model_has_permissions"
    __table_args__ = (
        PrimaryKeyConstraint("permission_id", "model_id", "model_type", name="model_has_permissions_permission_model_type_primary"),
    )

    permission_id: Mapped[int] = mapped_column(ForeignKey("permissions.id", ondelete="CASCADE"), nullable=False)
    model_type: Mapped[str] = mapped_column(String(255), nullable=False)
    model_id: Mapped[int] = mapped_column(BigInteger, nullable=False)


class ModelHasRole(Base):
    __tablename__ = "model_has_roles"
    __table_args__ = (
        PrimaryKeyConstraint("role_id", "model_id", "model_type", name="model_has_roles_role_model_type_primary"),
    )

    role_id: Mapped[int] = mapped_column(ForeignKey("roles.id", ondelete="CASCADE"), nullable=False)
    model_type: Mapped[str] = mapped_column(String(255), nullable=False)
    model_id: Mapped[int] = mapped_column(BigInteger, nullable=False)


class RoleHasPermission(Base):
    __tablename__ = "role_has_permissions"
    __table_args__ = (
        PrimaryKeyConstraint("permission_id", "role_id", name="role_has_permissions_permission_id_role_id_primary"),
    )

    permission_id: Mapped[int] = mapped_column(ForeignKey("permissions.id", ondelete="CASCADE"), nullable=False)
    role_id: Mapped[int] = mapped_column(ForeignKey("roles.id", ondelete="CASCADE"), nullable=False)


class Company(Base):
    __tablename__ = "companies"

    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str | None] = mapped_column(String(255))
    corporate_name: Mapped[str | None] = mapped_column(String(255))
    rg: Mapped[str | None] = mapped_column(String(255))
    cpf: Mapped[str | None] = mapped_column(String(255))
    trade_name: Mapped[str | None] = mapped_column(String(255))
    cnpj: Mapped[str | None] = mapped_column(String(255), unique=True)
    company_size: Mapped[str | None] = mapped_column(String(255))
    company_activities: Mapped[str | None] = mapped_column(String(255))
    opening_date: Mapped[date | None] = mapped_column(Date)
    share_capital: Mapped[Decimal | None] = mapped_column(DECIMAL(15, 2))
    state_registration: Mapped[str | None] = mapped_column(String(255))
    municipal_registration: Mapped[str | None] = mapped_column(String(255))
    address: Mapped[str] = mapped_column(String(255), nullable=False)
    number: Mapped[str] = mapped_column(String(255), nullable=False)
    complement: Mapped[str | None] = mapped_column(String(255))
    district: Mapped[str] = mapped_column(String(255), nullable=False)
    city: Mapped[str] = mapped_column(String(255), nullable=False)
    state: Mapped[str] = mapped_column(String(255), nullable=False)
    zip_code: Mapped[str] = mapped_column(String(255), nullable=False)
    phone: Mapped[str] = mapped_column(String(255), nullable=False)
    mobile_phone: Mapped[str | None] = mapped_column(String(255))
    email: Mapped[str] = mapped_column(String(255), nullable=False)
    secondary_email: Mapped[str | None] = mapped_column(String(255))
    website: Mapped[str | None] = mapped_column(String(255))
    comprasnet: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("0"))
    bec: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("0"))
    pregao_eletronico: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("0"))
    sicaf: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("0"))
    pncp: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("0"))
    additional_observations: Mapped[str | None] = mapped_column(Text)
    is_active: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("1"))
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    lances: Mapped[list[Lance]] = relationship(back_populates="company")


class Document(Base):
    __tablename__ = "documents"

    id: Mapped[int] = mapped_column(primary_key=True)
    arquivo: Mapped[str | None] = mapped_column(String(255))
    tipo_documento: Mapped[str | None] = mapped_column(
        Enum(
            "cartao_cnpj", "contrato_social_consolidacao", "rg_socio", "procuracao_representante", "certificado_simplificada_porte",
            "cnd_federal", "cnd_estadual", "cnd_municipal", "crf_fgts", "cndt_trabalhista", "declaracao_simples_nacional",
            "outros_certificados_fiscais", "balanco_patrimonial", "certidao_falencia_concordata", "declaracao_faturamento",
            "outros_doc_financeiros", "atestados_capacidade_tecnica", "art_responsabilidade_tecnica", "certificados_obra_servico",
            "iso", "cert_ambientais", "registro_federacao_conselho", "outras_certificacoes", "curriculo_profissional",
            "diplomas_certificados_curso", "registro_categoria_profissional", "outros_certificados_profissionais",
            "declaracao_inexistencia_fatos_impeditivos", "declaracao_cumprimento_art7", "declaracao_microempresa", "outras_declaracoes",
            "procuracao_conectar", "papel_timbrado", "documentos_adicionais",
            name="documents_tipo_documento_enum", native_enum=False,
        )
    )
    data_emissao: Mapped[date | None] = mapped_column(Date)
    data_validade: Mapped[date | None] = mapped_column(Date)
    orgao_emissor: Mapped[str | None] = mapped_column(String(255))
    observacoes: Mapped[str | None] = mapped_column(Text)
    status: Mapped[str] = mapped_column(
        Enum("valido", "expirado", "pendente_revisao", "nao_conforme", name="documents_status_enum", native_enum=False),
        nullable=False,
        server_default=text("'pendente_revisao'"),
    )
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    bids: Mapped[list[Bid]] = relationship(back_populates="document")


class Bid(Base):
    __tablename__ = "bids"

    id: Mapped[int] = mapped_column(primary_key=True)
    document_id: Mapped[int | None] = mapped_column(ForeignKey("documents.id", ondelete="CASCADE"))
    requesting_agency: Mapped[str | None] = mapped_column(String(255))
    bidding_modality: Mapped[str] = mapped_column(
        Enum("Concorrência Eletrônica", "Pregão Eletrônico", "Pregão Eletrônico Registro de Preços", "Dispensa de Licitação", name="bids_bidding_modality_enum", native_enum=False),
        nullable=False,
    )
    bidding_number: Mapped[str] = mapped_column(String(255), nullable=False)
    uasg_number: Mapped[str | None] = mapped_column(String(255))
    bidding_stage_start: Mapped[datetime | None] = mapped_column(DateTime)
    registration_deadline: Mapped[datetime | None] = mapped_column(DateTime)
    platform_email: Mapped[str | None] = mapped_column(String(255))
    registration_email: Mapped[str | None] = mapped_column(String(255))
    auctioneer_name: Mapped[str | None] = mapped_column(String(255))
    auctioneer_email: Mapped[str | None] = mapped_column(String(255))
    auctioneer_phone: Mapped[str | None] = mapped_column(String(255))
    items: Mapped[dict[str, Any] | list[Any] | None] = mapped_column(JSON)
    bidding_type: Mapped[str] = mapped_column(
        Enum("Aberta", "Exclusiva (ME/EPP)", "Mista", name="bids_bidding_type_enum", native_enum=False),
        nullable=False,
        server_default=text("'Aberta'"),
    )
    required_documents: Mapped[str | None] = mapped_column(Text)
    required_declarations: Mapped[str | None] = mapped_column(Text)
    is_active: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("1"))
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    document: Mapped[Document | None] = relationship(back_populates="bids")
    favorites: Mapped[list[Favorite]] = relationship(back_populates="bid")
    favorited_by: Mapped[list[User]] = relationship("User", secondary="favorites", back_populates="favorite_bids")


class Defense(Base):
    __tablename__ = "defenses"

    id: Mapped[int] = mapped_column(primary_key=True)
    tipo_contestacao: Mapped[str] = mapped_column(
        Enum("Inpugnação", "Recurso", "Representação", name="defenses_tipo_contestacao_enum", native_enum=False), nullable=False
    )
    numero_processo: Mapped[str | None] = mapped_column(String(255))
    problema_motivo_contestacao: Mapped[str] = mapped_column(
        Enum("Erro na especificação técnica", "Erro no orçamento estimativo", "Violação Legal", "Prazo Inadequado", "Exigência Desproporcional", name="defenses_problema_motivo_contestacao_enum", native_enum=False),
        nullable=False,
        server_default=text("'Erro na especificação técnica'"),
    )
    documento: Mapped[str | None] = mapped_column(String(255))
    prazo: Mapped[datetime | None] = mapped_column(DateTime)
    orgao: Mapped[str] = mapped_column(
        Enum("Prefeitura Municipal", "Governo do Estado", "Autarquia Federal", name="defenses_orgao_enum", native_enum=False),
        nullable=False,
        server_default=text("'Prefeitura Municipal'"),
    )
    arquivo: Mapped[str | None] = mapped_column(String(255))
    status: Mapped[str] = mapped_column(
        Enum("Em Elaboração", "Em Análise", "Aguardando Decisão", "Decidida", "Cancelada", "Concluida", name="defenses_status_enum", native_enum=False),
        nullable=False,
        server_default=text("'Em Elaboração'"),
    )
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)


class Modality(Base):
    __tablename__ = "modalities"

    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    description: Mapped[str | None] = mapped_column(Text)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    radars: Mapped[list[Radar]] = relationship(back_populates="modality")


class AreaInterest(Base):
    __tablename__ = "area_interests"

    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    description: Mapped[str | None] = mapped_column(Text)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    radars: Mapped[list[Radar]] = relationship(back_populates="area_interest")


class State(Base):
    __tablename__ = "states"

    id: Mapped[int] = mapped_column(primary_key=True)
    cod_uf: Mapped[str] = mapped_column(String(255), nullable=False)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    uf: Mapped[str] = mapped_column(String(255), nullable=False)
    region: Mapped[str] = mapped_column(String(255), nullable=False)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    radars: Mapped[list[Radar]] = relationship(back_populates="state_rel")


class Radar(Base):
    __tablename__ = "radars"

    id: Mapped[int] = mapped_column(primary_key=True)
    modality_id: Mapped[int | None] = mapped_column(ForeignKey("modalities.id", ondelete="SET NULL"))
    state_id: Mapped[int | None] = mapped_column(ForeignKey("states.id", ondelete="SET NULL"))
    titulo: Mapped[str] = mapped_column(String(255), nullable=False)
    area_interest_id: Mapped[int | None] = mapped_column(ForeignKey("area_interests.id", ondelete="SET NULL"))
    situacao: Mapped[str] = mapped_column(
        Enum("Novo", "Urgente", "Em Andamento", "Concluído", name="radars_situacao_enum", native_enum=False), nullable=False, server_default=text("'Novo'")
    )
    orgao: Mapped[str] = mapped_column(
        Enum("Prefeitura Municipal", "Governo do Estado de Minas Gerais", "Ministério Público", name="radars_orgao_enum", native_enum=False), nullable=False, server_default=text("'Prefeitura Municipal'")
    )
    valor: Mapped[Decimal | None] = mapped_column(DECIMAL(15, 2))
    relevancia: Mapped[int] = mapped_column(Integer, nullable=False, server_default=text("0"))
    data_hora_encerramento: Mapped[datetime | None] = mapped_column(DateTime)
    tempo_restante: Mapped[str | None] = mapped_column(String(255))
    descricao: Mapped[str | None] = mapped_column(Text)
    observacoes: Mapped[str | None] = mapped_column(Text)
    pncp_id_compra: Mapped[str | None] = mapped_column(String(255), unique=True)
    numero_controle_pncp: Mapped[str | None] = mapped_column(String(255))
    status_pncp: Mapped[str | None] = mapped_column(String(255))
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    state_rel: Mapped[State | None] = relationship(back_populates="radars")
    area_interest: Mapped[AreaInterest | None] = relationship(back_populates="radars")
    modality: Mapped[Modality | None] = relationship(back_populates="radars")


class Lance(Base):
    __tablename__ = "lances"

    id: Mapped[int] = mapped_column(primary_key=True)
    company_id: Mapped[int | None] = mapped_column(ForeignKey("companies.id", ondelete="SET NULL"))
    tipo_participacao: Mapped[str | None] = mapped_column(String(255))
    estrategia_lance: Mapped[str | None] = mapped_column(String(255))
    lance_maximo: Mapped[Decimal | None] = mapped_column(DECIMAL(15, 2))
    limite_tempo: Mapped[int | None] = mapped_column(Integer)
    incremento_padrao: Mapped[Decimal | None] = mapped_column(DECIMAL(15, 2))
    margem_seguranca: Mapped[Decimal | None] = mapped_column(DECIMAL(5, 2))
    incremento_automatico: Mapped[bool | None] = mapped_column(Boolean)
    notificacoes_tempo_real: Mapped[bool | None] = mapped_column(Boolean)
    licitacao_vencida: Mapped[str | None] = mapped_column(String(255))
    lance_vencedor: Mapped[Decimal | None] = mapped_column(DECIMAL(15, 2))
    prazo_entrega: Mapped[int | None] = mapped_column(Integer)
    condicoes_pagamento: Mapped[str | None] = mapped_column(Text)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    company: Mapped[Company | None] = relationship(back_populates="lances")


class Notification(Base):
    __tablename__ = "notifications"

    id: Mapped[str] = mapped_column(String(36), primary_key=True)
    type: Mapped[str] = mapped_column(String(255), nullable=False)
    notifiable_type: Mapped[str] = mapped_column(String(255), nullable=False)
    notifiable_id: Mapped[int] = mapped_column(BigInteger, nullable=False)
    data: Mapped[str] = mapped_column(Text, nullable=False)
    read_at: Mapped[datetime | None] = mapped_column(DateTime)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)


class Favorite(Base):
    __tablename__ = "favorites"
    __table_args__ = (UniqueConstraint("user_id", "bid_id", name="favorites_user_id_bid_id_unique"),)

    id: Mapped[int] = mapped_column(primary_key=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    bid_id: Mapped[int] = mapped_column(ForeignKey("bids.id", ondelete="CASCADE"), nullable=False)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    user: Mapped[User] = relationship(back_populates="favorites")
    bid: Mapped[Bid] = relationship(back_populates="favorites")


class SavedPregao(Base):
    __tablename__ = "saved_pregoes"
    __table_args__ = (UniqueConstraint("user_id", "id_compra", name="saved_pregoes_user_id_id_compra_unique"),)

    id: Mapped[int] = mapped_column(primary_key=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    id_compra: Mapped[str] = mapped_column(String(32), nullable=False)
    numero_controle_pncp: Mapped[str | None] = mapped_column(String(64))
    orgao: Mapped[str | None] = mapped_column(String(255))
    uf: Mapped[str | None] = mapped_column(String(2))
    municipio: Mapped[str | None] = mapped_column(String(120))
    modalidade: Mapped[str | None] = mapped_column(String(120))
    modo_disputa: Mapped[str | None] = mapped_column(String(120))
    processo: Mapped[str | None] = mapped_column(String(80))
    srp: Mapped[bool] = mapped_column(Boolean, nullable=False, server_default=text("0"))
    valor_estimado: Mapped[Decimal | None] = mapped_column(DECIMAL(18, 2))
    data_publicacao: Mapped[datetime | None] = mapped_column(DateTime)
    data_abertura: Mapped[datetime | None] = mapped_column(DateTime)
    data_encerramento: Mapped[datetime | None] = mapped_column(DateTime)
    objeto: Mapped[str | None] = mapped_column(Text)
    payload: Mapped[dict[str, Any] | list[Any] | None] = mapped_column(JSON)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    user: Mapped[User] = relationship(back_populates="saved_pregoes")


class RadarPreference(Base):
    __tablename__ = "radar_preferences"

    id: Mapped[int] = mapped_column(primary_key=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"), nullable=False, unique=True)
    keyword: Mapped[str] = mapped_column(String(255), nullable=False)
    regions: Mapped[list[Any] | dict[str, Any] | None] = mapped_column(JSON)
    ufs: Mapped[list[Any] | dict[str, Any] | None] = mapped_column(JSON)
    last_synced_at: Mapped[datetime | None] = mapped_column(DateTime)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    user: Mapped[User] = relationship(back_populates="radar_preference")


class RadarResult(Base):
    __tablename__ = "radar_results"
    __table_args__ = (
        UniqueConstraint("user_id", "id_compra", name="radar_results_user_id_id_compra_unique"),
        Index("radar_results_user_id_data_publicacao_index", "user_id", "data_publicacao"),
        Index("radar_results_user_id_uf_index", "user_id", "uf"),
    )

    id: Mapped[int] = mapped_column(primary_key=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    id_compra: Mapped[str] = mapped_column(String(60), nullable=False)
    numero_controle_pncp: Mapped[str | None] = mapped_column(String(80))
    orgao: Mapped[str | None] = mapped_column(String(255))
    uf: Mapped[str | None] = mapped_column(String(2))
    municipio: Mapped[str | None] = mapped_column(String(120))
    modalidade: Mapped[str | None] = mapped_column(String(120))
    data_publicacao: Mapped[datetime | None] = mapped_column(DateTime)
    data_encerramento: Mapped[datetime | None] = mapped_column(DateTime)
    objeto: Mapped[str | None] = mapped_column(Text)
    payload: Mapped[dict[str, Any] | list[Any] | None] = mapped_column(JSON)
    created_at: Mapped[datetime | None] = mapped_column(DateTime)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime)

    user: Mapped[User] = relationship(back_populates="radar_results")
