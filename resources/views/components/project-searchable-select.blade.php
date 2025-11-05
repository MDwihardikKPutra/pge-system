@props([
    'name' => 'project_id',
    'value' => null,
    'required' => false,
    'placeholder' => 'Cari atau pilih project...',
    'class' => '',
])

@php
    $selectedProject = $value ? \App\Models\Project::find($value) : null;
@endphp

<div x-data="projectSearchableSelect({{ $value ? "'{$selectedProject->name} ({$selectedProject->code})'" : 'null' }}, {{ $value ?? 'null' }})" 
     class="relative {{ $class }}">
    <input type="hidden" name="{{ $name }}" :value="selectedId" :required="{{ $required ? 'true' : 'false' }}">
    
    <div class="relative">
        <input 
            type="text"
            x-model="searchQuery"
            @input="searchProjects()"
            @focus="showDropdown = true"
            @blur="setTimeout(() => showDropdown = false, 200)"
            :placeholder="{{ json_encode($placeholder) }}"
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10"
            autocomplete="off"
        >
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <!-- Dropdown Results -->
    <div x-show="showDropdown && (searching || projects.length > 0)" 
         x-cloak
         x-transition
         class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
        <div x-show="searching" class="p-3 text-center text-sm text-gray-500">
            Mencari...
        </div>
        <template x-if="!searching && projects.length === 0 && searchQuery.length > 0">
            <div class="p-3 text-center text-sm text-gray-500">
                Tidak ada project ditemukan
            </div>
        </template>
        <template x-if="!searching && projects.length > 0">
            <ul class="py-1">
                <template x-for="project in projects" :key="project.id">
                    <li>
                        <button 
                            type="button"
                            @click="selectProject(project)"
                            class="w-full text-left px-4 py-2 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none transition-colors"
                            :class="{ 'bg-blue-50': selectedId == project.id }"
                        >
                            <div class="font-medium text-gray-900" x-text="project.name"></div>
                            <div class="text-xs text-gray-500" x-text="project.code"></div>
                        </button>
                    </li>
                </template>
            </ul>
        </template>
    </div>

    <!-- Clear Button -->
    <button 
        type="button"
        x-show="selectedId"
        @click="clearSelection()"
        class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

