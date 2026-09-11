<script setup>
import { defineProps } from 'vue';
import { usePosStore } from '../../Composables/usePosStore';

const props = defineProps({
    locations: Object,
    user: Object
});

const { state } = usePosStore();
</script>

<template>
    <header class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 shadow-sm z-10 p-4 flex justify-between items-center transition-colors duration-300">
        <div class="flex items-center space-x-6">
            <h1 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 tracking-tight">POS<span class="text-gray-800 dark:text-gray-200">V2</span></h1>
            <div class="flex items-center bg-gray-50 dark:bg-slate-800 rounded-lg p-1 border border-gray-200 dark:border-slate-700">
                <span class="text-sm text-gray-500 dark:text-gray-400 px-3">Location</span>
                <select v-model="state.locationId" class="bg-transparent border-none text-sm font-semibold text-gray-800 dark:text-gray-200 focus:ring-0 py-1 pr-8 cursor-pointer">
                    <option v-for="(name, id) in locations" :key="id" :value="id" class="text-gray-900">
                        {{ name }}
                    </option>
                </select>
            </div>
        </div>
        
        <div class="flex items-center space-x-4">
            <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold uppercase">
                    {{ user?.first_name?.charAt(0) || 'U' }}
                </div>
                <span>{{ user?.first_name || 'User' }} {{ user?.last_name || '' }}</span>
            </div>
            
            <a href="/home" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors shadow-sm">
                Dashboard
            </a>
        </div>
    </header>
</template>
