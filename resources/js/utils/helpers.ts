export function formatarDinheiro(valor: number | string): string {
  const numero = Number(valor);

  if (isNaN(numero)) return 'R$ 0,00';

  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(numero);
}

export function formatarData(date?: string | null): string | null {
  if (!date) return null
  const [year, month, day] = date.split('-').map(Number)

  if (!day || !month || !year) {
    throw new Error(`Data inválida: ${date}`)
  }

  return `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`
}

export function formatarMesAno(mesReferencia: string): string {
  if (!mesReferencia) return ''

  const [ano, mes] = mesReferencia.split('-')
  const data = new Date(Number(ano), Number(mes) - 1)

  const texto = data.toLocaleDateString('pt-BR', {
    month: 'long',
    year: 'numeric'
  })

  return texto.charAt(0).toUpperCase() + texto.slice(1)
}

export function sanitizarData(data: string){
  if(!data) return;
  return data.replaceAll("/", "-");
}

export const toNumber = (value: any): number => {
  if (!value) return 0
  return Number(value)
}
