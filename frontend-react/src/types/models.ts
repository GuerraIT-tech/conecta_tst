export interface User {
  id: number
  name: string
  email: string
  cnpj?: string | null
}

export interface Bid {
  id: number
  bidding_number: string
  bidding_modality: string
  requesting_agency?: string | null
  registration_email?: string | null
  auctioneer_name?: string | null
}

export interface Company {
  id: number
  corporate_name?: string | null
  trade_name?: string | null
  cnpj?: string | null
  city?: string | null
  state?: string | null
  email?: string | null
  address?: string | null
  number?: string | null
  district?: string | null
  zip_code?: string | null
  phone?: string | null
  cnpj?: string | null
}

export interface Radar {
  id: number
  titulo: string
  situacao: string
  state_id?: number | null
  modality_id?: number | null
}

export interface ChartSeries {
  labels: string[]
  values: number[]
}

export interface DashboardReport {
  kpis: {
    total_clients: number
    active_bids: number
    total_users: number
    radar_items: number
  }
  latest_clients: Array<Pick<Company, 'id' | 'corporate_name' | 'city' | 'state'>>
  latest_bids: Array<Pick<Bid, 'id' | 'bidding_number' | 'bidding_modality' | 'requesting_agency'>>
  charts: {
    revenue_projection: ChartSeries
    pipeline_by_status: ChartSeries
  }
}
