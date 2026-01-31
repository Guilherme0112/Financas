let loaded = false
let loadingPromise: Promise<void> | null = null

export function useGoogleCharts() {
  const load = () => {
    if (loaded) return Promise.resolve()

    if (!loadingPromise) {
      loadingPromise = new Promise((resolve) => {
        const script = document.createElement('script')
        script.src = 'https://www.gstatic.com/charts/loader.js'
        script.onload = () => {
          window.google.charts.load('current', { packages: ['corechart'] })
          window.google.charts.setOnLoadCallback(() => {
            loaded = true
            resolve()
          })
        }
        document.head.appendChild(script)
      })
    }

    return loadingPromise
  }

  return { load }
}
