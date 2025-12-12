export function formatarDinheiro(valor: number | string): string {
  const numero = Number(valor)

  if (isNaN(numero)) return 'R$ 0,00'

  return 'R$ ' + numero.toFixed(2).replace('.', ',')
}
