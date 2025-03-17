<x-filament::modal id="viewSchedule" width="md">
    <x-slot name="title">📅 Detalhes do Agendamento</x-slot>

    <x-filament::infolist>
        <x-filament::section title="📌 Informações do Agendamento">
            <x-filament::entry name="scheduled_at" label="📅 Data e Hora" />
            <x-filament::entry name="recurrence_frequency" label="⏳ Frequência" />
        </x-filament::section>

        <x-filament::section title="👨‍🏫 Professor">
            <x-filament::entry name="professor.name" label="Nome" />
            <x-filament::entry name="professor.email" label="Email" />
        </x-filament::section>

        <x-filament::section title="🎓 Aluno">
            <x-filament::entry name="student.name" label="Nome" />
            <x-filament::entry name="student.email" label="Email" />
        </x-filament::section>
    </x-filament::infolist>

    <x-slot name="footer">
        <x-filament::button wire:click="editSchedule" color="warning">✏️ Editar</x-filament::button>
        <x-filament::button wire:click="deleteSchedule" color="danger">🗑️ Excluir</x-filament::button>
    </x-slot>
</x-filament::modal>
