<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Módulo PUR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fa-solid fa-file-circle-plus mr-2"></i> Registrar Nuevo Expediente
                    </h2>
                </div>

                {{-- Formulario estándar de Laravel: sin intercepciones JS --}}
                <form action="{{ route('pur.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título del Proyecto <span class="text-red-500">*</span></label>
                            <input type="text" name="titulo" value="{{ old('titulo') }}" required
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Ej. Implementación de un sistema...">
                            @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Proyecto</label>
                            <select name="tipo" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="cuantitativo" {{ old('tipo') == 'cuantitativo' ? 'selected' : '' }}>Cuantitativo</option>
                                <option value="cualitativo" {{ old('tipo') == 'cualitativo' ? 'selected' : '' }}>Cualitativo</option>
                                <option value="mixto" {{ old('tipo') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                            </select>
                            @error('tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Documento (Solo PDF) <span class="text-red-500">*</span></label>
                            <input type="file" name="archivo_pdf" accept="application/pdf" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('archivo_pdf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 gap-3 border-t border-gray-100 pt-4">
                        {{-- El botón cancelar ahora es un enlace puro tipo botón --}}
                        <a href="{{ route('pur.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancelar
                        </a>

                        {{-- Botón submit estándar que activa el ciclo request -> validación -> controller --}}
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Guardar Expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>