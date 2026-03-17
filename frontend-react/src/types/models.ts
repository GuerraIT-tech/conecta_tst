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
}

export interface Company {
  id: number
  corporate_name?: string | null
  cnpj?: string | null
}

export interface Radar {
  id: number
  titulo: string
  situacao: string
  state_id?: number | null
  modality_id?: number | null
}
