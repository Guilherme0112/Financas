export interface Lancamento {
    id: number
    nome: string
    descricao: string
    valor: number
    tipo: 'ENTRADA' | 'SAIDA'
    recorrente: boolean
    mes_referencia?: string
}

// todo: criar relacionamento com categoria