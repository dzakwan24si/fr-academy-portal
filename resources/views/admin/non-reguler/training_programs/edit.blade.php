<x-admin-layout>
    <x-slot name="title">Edit Program Training</x-slot>
    <x-slot name="header">Edit Program Training</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('training-non-reguler.training-programs.update', $trainingProgram->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Program</label>
                    <input type="text" name="name" value="{{ $trainingProgram->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun</label>
                    <input type="number" name="year" value="{{ $trainingProgram->year }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $trainingProgram->description }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-[#28844b] text-white rounded-lg font-bold">Update</button>
                <a href="{{ route('training-non-reguler.training-programs.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg font-bold">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>
