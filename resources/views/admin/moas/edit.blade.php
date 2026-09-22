<x-admin-layout>
    <x-slot name="title">Edit MOA</x-slot>
    <x-slot name="header">Edit MOA</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('training-reguler.moas.update', $moa->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program Training</label>
                    <select name="training_program_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ $moa->training_program_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trainer</label>
                    <select name="trainer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($trainers as $t)
                            <option value="{{ $t->id }}" {{ $moa->trainer_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="week_start_date" value="{{ $moa->week_start_date }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="week_end_date" value="{{ $moa->week_end_date }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="draft" {{ $moa->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $moa->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $moa->notes }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-[#28844b] text-white rounded-lg font-bold">Update</button>
                <a href="{{ route('training-reguler.moas.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg font-bold">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>