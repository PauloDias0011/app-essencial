<x-filament::modal id="createSchedule" width="md">
    <x-slot name="title">➕ Criar Novo Agendamento</x-slot>

    <form wire:submit.prevent="createSchedule">
        <x-filament::form>
            <x-filament::input name="scheduled_at" label="📅 Data e Hora" type="datetime-local" required />
            <x-filament::select name="professor_id" label="👨‍🏫 Professor" :options="$this->getProfessors()" required />
            <x-filament::select name="student_id" label="🎓 Aluno" :options="$this->getStudents()" required />
            <x-filament::checkbox name="is_recurring" label="🔄 Agendamento Recorrente" wire:model="isRecurring" />
            <x-filament::select name="recurrence_frequency" label="⏳ Frequência"
                :options="['daily' => 'Diário', 'weekly' => 'Semanal', 'monthly' => 'Mensal']"
                x-show="isRecurring" required />

            <x-filament::button type="submit" color="primary">Salvar</x-filament::button>
        </x-filament::form>
    </form>
</x-filament::modal>
