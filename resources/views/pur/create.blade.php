<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h2 class="text-2xl font-bold mb-4 text-gray-800">Radicar Nuevo Expediente (PUR)</h2>

                @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('pur.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="titulo" class="block text-gray-700 text-sm font-bold mb-2">Título del Proyecto:</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('titulo')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Investigación:</label>
                        <select name="tipo" id="tipo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Seleccione un tipo...</option>
                            <option value="cuantitativo" {{ old('tipo') == 'cuantitativo' ? 'selected' : '' }}>Cuantitativo</option>
                            <option value="cualitativo" {{ old('tipo') == 'cualitativo' ? 'selected' : '' }}>Cualitativo</option>
                            <option value="mixto" {{ old('tipo') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                        </select>
                        @error('tipo')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="archivo_pdf" class="block text-gray-700 text-sm font-bold mb-2">Documento del Proyecto (Solo PDF, Max 10MB):</label>
                        <input type="file" name="archivo_pdf" id="archivo_pdf" accept=".pdf"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('archivo_pdf')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150">
                            Radicar Expediente
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>