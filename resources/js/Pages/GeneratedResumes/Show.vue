<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

// Recebemos o objeto gerado pelo backend
const props = defineProps({
    generatedResume: Object,
});

// Acessamos os dados facilmente
const resume = props.generatedResume;
</script>

<template>
    <Head title="Resultado da Análise" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Análise ATS e Currículo Otimizado
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- CABEÇALHO DO RESULTADO -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center"
                >
                    <div>
                        <h3 class="text-2xl font-bold text-indigo-600">
                            Nota ATS: {{ resume.ats_score }} / 100
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Essa é a probabilidade do seu currículo passar pelos
                            robôs da empresa.
                        </p>

                        <div
                            v-if="resume.has_seniority_gap"
                            class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"
                        >
                            ⚠️ Identificamos um Gap de Senioridade para esta
                            vaga.
                        </div>
                        <div
                            v-else
                            class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                        >
                            ✅ Sua senioridade parece alinhada com a vaga.
                        </div>
                    </div>
                    <div>
                        <Link
                            :href="route('resumes.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                        >
                            Fazer Nova Análise
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- COLUNA ESQUERDA: FEEDBACK -->
                    <div class="md:col-span-1 space-y-6">
                        <div
                            class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500"
                        >
                            <h4 class="font-bold text-gray-900 mb-2">
                                💪 Pontos Fortes
                            </h4>
                            <ul
                                class="list-disc pl-5 space-y-1 text-sm text-gray-600"
                            >
                                <li
                                    v-for="forte in resume.feedback?.fortes"
                                    :key="forte"
                                >
                                    {{ forte }}
                                </li>
                            </ul>
                        </div>

                        <div
                            class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500"
                        >
                            <h4 class="font-bold text-gray-900 mb-2">
                                🎯 Pontos a Melhorar
                            </h4>
                            <ul
                                class="list-disc pl-5 space-y-1 text-sm text-gray-600"
                            >
                                <li
                                    v-for="fraco in resume.feedback?.fracos"
                                    :key="fraco"
                                >
                                    {{ fraco }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- COLUNA DIREITA: CURRÍCULO GERADO -->
                    <div
                        class="md:col-span-2 bg-white shadow-sm sm:rounded-lg p-6"
                    >
                        <h4 class="font-bold text-gray-900 mb-4 border-b pb-2">
                            Seu Novo Currículo Otimizado
                        </h4>
                        <div
                            class="whitespace-pre-wrap text-gray-700 text-sm leading-relaxed"
                        >
                            {{ resume.optimized_content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
