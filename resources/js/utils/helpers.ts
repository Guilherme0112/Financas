export function formatarDinheiro(valor: number | string): string {
  const numero = Number(valor)

  if (isNaN(numero)) return 'R$ 0,00'

  return 'R$ ' + numero.toFixed(2).replace('.', ',')
}

 export function formatarData(date?: string | null): string | null {
    if (!date) return null
    const [year, month, day] = date.split('-').map(Number)

    if (!day || !month || !year) {
      throw new Error(`Data inválida: ${date}`)
    }

    return `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`
  }