<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// O Inertia useForm gerencia o estado dos nossos inputs e os envia pro Laravel
const form = useForm({
    resume_content: "",
    job_url: "",
    job_description: "",
});

// Função chamada quando clicamos no botão de "Gerar"
const submit = () => {
    form.post(route("resumes.store"));
};
</script>

<template>
    <Head title="Gerar Novo Currículo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Preparar Currículo para Vaga
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit" class="space-y-6">
                        <div
                            v-if="form.errors.error"
                            class="p-4 rounded-md bg-red-50 border border-red-200"
                        >
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-5 w-5 text-red-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3
                                        class="text-sm font-medium text-red-800"
                                    >
                                        Ocorreu um erro ao processar:
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ form.errors.error }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- SEÇÃO 1: O Currículo Atual -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">
                                1. Seu Currículo Atual
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                Cole aqui o texto do seu currículo atual. A IA
                                usará isso como base.
                            </p>

                            <textarea
                                v-model="form.resume_content"
                                rows="8"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Experiência: Desenvolvedor Web..."
                            ></textarea>
                            <InputError
                                class="mt-2"
                                :message="form.errors.resume_content"
                            />
                        </div>

                        <hr class="border-gray-200" />

                        <!-- SEÇÃO 2: A Vaga Desejada -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">
                                2. Informações da Vaga
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                Cole o link da vaga ou a descrição completa para
                                analisarmos o ATS.
                            </p>

                            <div class="mb-4">
                                <label
                                    class="block font-medium text-sm text-gray-700"
                                    >Link da Vaga (Opcional, usaremos Web
                                    Scraping depois)</label
                                >
                                <input
                                    v-model="form.job_url"
                                    type="url"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="https://linkedin.com/jobs/..."
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.job_url"
                                />
                            </div>

                            <div>
                                <label
                                    class="block font-medium text-sm text-gray-700"
                                    >Descrição da Vaga</label
                                >
                                <textarea
                                    v-model="form.job_description"
                                    rows="6"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="Requisitos: Experiência com Vue.js, Laravel..."
                                ></textarea>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.job_description"
                                />
                            </div>
                        </div>

                        <!-- BOTÃO DE ENVIO -->
                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Analisar e Gerar Currículo
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
