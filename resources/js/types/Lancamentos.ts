export interface Lancamento {
    id: number
    nome: string
    descricao: string
    valor: number
    tipo: 'ENTRADA' | 'SAIDA'
    recorrente: boolean
    mes_referencia?: string
    categoria_entrada: string,
    categoria_saida: string,
}