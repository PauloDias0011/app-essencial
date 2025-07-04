{{-- resources/views/livewire/empty-panel-content.blade.php --}}

<div class="fi-empty-state mx-auto grid max-w-lg justify-items-center text-center">
    <div class="fi-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-6 dark:bg-gray-800">
        <svg class="fi-empty-state-icon h-16 w-16 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443a55.381 55.381 0 0 1 5.25 2.882V15m-9 0h9m-9 0a2.25 2.25 0 0 0 2.25 2.25h4.5a2.25 2.25 0 0 0 2.25-2.25m-9 0V9.375a2.25 2.25 0 0 1 2.25-2.25h4.5a2.25 2.25 0 0 1 2.25 2.25v5.625m-9 0h9" />
        </svg>
    </div>

    <div class="fi-empty-state-content grid max-w-md gap-y-6">
        <div>
            <h1 class="fi-empty-state-heading fi-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Bem-vindo ao Apoio Pedagógico Essencial
            </h1>
            <p class="fi-empty-state-description text-sm text-gray-500 dark:text-gray-400 mt-2">
                Transformando vidas através da educação personalizada e de qualidade.
            </p>
        </div>

        {{-- Seção de Vantagens --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Metodologia Personalizada --}}
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center space-y-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Metodologia Personalizada
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Estratégias de ensino adaptadas ao perfil de cada aluno, respeitando seu ritmo e estilo de aprendizagem.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Acompanhamento Contínuo --}}
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center space-y-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Acompanhamento Contínuo
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Monitoramento constante do progresso acadêmico com relatórios detalhados e feedback regular.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Professores Especialistas --}}
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center space-y-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Professores Especialistas
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Equipe multidisciplinar de educadores qualificados e experientes em apoio pedagógico.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Recursos Inovadores --}}
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center space-y-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Recursos Inovadores
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Tecnologias educacionais e materiais didáticos modernos para facilitar o aprendizado.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Suporte Emocional --}}
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center space-y-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Suporte Emocional
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Ambiente acolhedor que fortalece a autoestima e a confiança dos estudantes.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Horários Flexíveis --}}
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center space-y-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Horários Flexíveis
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Agendamentos adaptáveis à rotina da família, incluindo opções presenciais e online.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        

     
</div>