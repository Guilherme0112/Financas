<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

defineProps<{
  canLogin?: boolean
  canRegister?: boolean
}>()
</script>

<template>

  <Head title="Organizei - Controle Financeiro Inteligente" />

  <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 text-gray-800">

    <!-- HEADER -->
    <header class="max-w-7xl mx-auto flex items-center justify-between px-6 py-6">
      <h1 class="text-2xl font-bold text-green-700">Organizei</h1>

      <nav class="space-x-4" v-if="canLogin">
        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
          Dashboard
        </Link>

        <template v-else>
          <Link :href="route('login')"
            class="px-4 py-2 border border-green-600 text-green-700 rounded-lg hover:bg-green-100">
            Entrar
          </Link>

          <Link v-if="canRegister" :href="route('register')"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Criar conta
          </Link>
        </template>
      </nav>
    </header>

    <!-- HERO -->
    <section class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 px-6 py-20 items-center">
      <div>
        <h2 class="text-4xl font-bold text-gray-900 leading-tight">
          Controle total da sua vida financeira em um só lugar
        </h2>

        <p class="mt-6 text-lg text-gray-600">
          Organize gastos, acompanhe sua evolução mensal e visualize para onde seu dinheiro está indo.
          Simples, rápido e sem complicação.
        </p>

        <div class="mt-8 flex gap-4">
          <Link v-if="canRegister" :href="route('register')"
            class="px-6 py-3 bg-green-600 text-white rounded-xl text-lg font-semibold hover:bg-green-700">
            Começar agora
          </Link>

          <Link :href="route('login')"
            class="px-6 py-3 border border-green-600 text-green-700 rounded-xl text-lg font-semibold hover:bg-green-100">
            Já tenho conta
          </Link>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-green-50 p-4 rounded-xl">
            <p class="text-sm text-gray-500">Saldo atual</p>
            <p class="text-xl font-bold text-green-700">R$ 2.350,00</p>
          </div>

          <div class="bg-red-50 p-4 rounded-xl">
            <p class="text-sm text-gray-500">Gastos do mês</p>
            <p class="text-xl font-bold text-red-600">R$ 1.780,00</p>
          </div>

          <div class="bg-blue-50 p-4 rounded-xl">
            <p class="text-sm text-gray-500">Economia</p>
            <p class="text-xl font-bold text-blue-600">R$ 570,00</p>
          </div>

          <div class="bg-yellow-50 p-4 rounded-xl">
            <p class="text-sm text-gray-500">Metas</p>
            <p class="text-xl font-bold text-yellow-600">3 ativas</p>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="bg-white py-20">
      <div class="max-w-7xl mx-auto px-6 text-center">
        <h3 class="text-3xl font-bold text-gray-900">O que você pode fazer</h3>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-10">
          <div class="p-8 bg-green-50 rounded-xl shadow">
            <h4 class="text-xl font-semibold text-green-700">Controle de Gastos</h4>
            <p class="mt-4 text-gray-600">
              Registre entradas, saídas, categorize seus gastos e veja tudo em tempo real.
            </p>
          </div>

          <div class="p-8 bg-green-50 rounded-xl shadow">
            <h4 class="text-xl font-semibold text-green-700">Dashboard Inteligente</h4>
            <p class="mt-4 text-gray-600">
              Gráficos claros mostrando sua evolução mensal e onde você está gastando mais.
            </p>
          </div>

          <div class="p-8 bg-green-50 rounded-xl shadow">
            <h4 class="text-xl font-semibold text-green-700">Planejamento Financeiro</h4>
            <p class="mt-4 text-gray-600">
              Crie metas, acompanhe economias e evite surpresas no fim do mês.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- PLANOS -->
    <section class="py-20 bg-green-50">
      <div class="max-w-5xl mx-auto px-6 text-center">
        <h3 class="text-3xl font-bold text-gray-900">Escolha seu plano</h3>
        <p class="mt-4 text-gray-600">
          Teste grátis ou já vá direto no profissional.
        </p>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">

          <!-- PLANO GRÁTIS -->
          <div class="bg-white rounded-2xl shadow-xl p-10 border border-green-200 flex flex-col">
            <h4 class="text-2xl font-bold text-gray-900">Plano Gratuito</h4>

            <div class="mt-6">
              <span class="text-5xl font-extrabold text-green-600">R$ 0</span>
              <span class="text-gray-600">/ por 7 dias</span>
            </div>

            <p class="mt-2 text-sm text-green-700 font-semibold">
              Teste completo por 7 dias
            </p>

            <ul class="mt-8 space-y-3 text-left text-gray-700 flex-1">
              <li>✅ Controle básico de gastos</li>
              <li>✅ Dashboard simples</li>
              <li>✅ Cadastro de despesas e receitas</li>
              <li>✅ Cancelamento automático</li>
            </ul>

            <Link v-if="canRegister" :href="route('register')"
              class="mt-10 w-full text-center px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
              Testar grátis
            </Link>
          </div>

          <!-- PLANO PAGO -->
          <div class="bg-white rounded-2xl shadow-xl p-10 border-2 border-green-600 flex flex-col relative">
            <span
              class="absolute -top-4 left-1/2 -translate-x-1/2 bg-green-600 text-white text-xs font-bold px-4 py-1 rounded-full">
              MAIS POPULAR
            </span>

            <h4 class="text-2xl font-bold text-gray-900">Plano Premium</h4>

            <div class="mt-6">
              <span class="text-5xl font-extrabold text-gray-900">R$ 20</span>
              <span class="text-gray-600">/mês</span>
            </div>

            <p class="mt-2 text-sm text-green-700 font-semibold">
              Tudo liberado, sem limites
            </p>

            <ul class="mt-8 space-y-3 text-left text-gray-700 flex-1">
              <li>✅ Tudo do plano grátis</li>
              <li>✅ Dashboard avançado</li>
              <li>✅ Metas financeiras</li>
              <li>✅ Relatórios mensais</li>
              <li>✅ Suporte prioritário</li>
            </ul>

            <Link v-if="canRegister" :href="route('register')"
              class="mt-10 w-full text-center px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
              Assinar agora
            </Link>

            <Link v-else :href="route('login')"
              class="mt-10 w-full text-center px-6 py-3 border border-green-600 text-green-700 rounded-xl font-bold hover:bg-green-100">
              Acessar conta
            </Link>
          </div>

        </div>
      </div>
    </section>

    <!-- CTA FINAL -->
    <section class="py-20 bg-green-600 text-white text-center">
      <h3 class="text-3xl font-bold">Pare de perder dinheiro hoje mesmo</h3>
      <p class="mt-4 text-lg opacity-90">
        Comece agora a usar o Organizei e tenha controle total sobre sua vida financeira.
      </p>

      <Link v-if="canRegister" :href="route('register')"
        class="inline-block mt-8 px-8 py-4 bg-white text-green-700 rounded-xl text-lg font-bold hover:bg-green-100">
        Criar minha conta grátis
      </Link>
    </section>

    <!-- FOOTER -->
    <footer class="py-10 text-center text-gray-500 text-sm">
      © {{ new Date().getFullYear() }} Organizei — Todos os direitos reservados.
    </footer>
  </div>
</template>
