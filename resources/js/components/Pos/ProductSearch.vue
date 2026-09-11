<script setup>
import { ref, defineEmits } from 'vue';
import CameraScannerModal from './CameraScannerModal.vue';

const emit = defineEmits(['search']);
const isScannerOpen = ref(false);

let debounceTimer;
const handleInput = (event) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        emit('search', event.target.value);
    }, 300);
};

const openCameraScanner = () => {
    isScannerOpen.value = true;
};

const handleScan = (text) => {
    emit('search', text);
    isScannerOpen.value = false;
};

const openWeighingScale = () => {
    alert('Weighing Scale Modal will open here');
};
</script>

<template>
    <div class="bg-white dark:bg-slate-900 p-4 border-b border-gray-200 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div class="flex items-center space-x-3">
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    type="text" 
                    @input="handleInput"
                    class="block w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-700 rounded-xl leading-5 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-base transition-all shadow-sm" 
                    placeholder="Search products by name, SKU or scan barcode..." 
                    autofocus
                />
            </div>
            
            <button @click="openCameraScanner" class="flex-shrink-0 p-3 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 transition-all shadow-sm group relative" title="Camera Scanner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="absolute -top-2 -right-2 flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </span>
            </button>
            
            <button @click="openWeighingScale" class="flex-shrink-0 p-3 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all shadow-sm" title="Weighing Scale">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
            </button>
        </div>
        
        <CameraScannerModal 
            v-if="isScannerOpen" 
            @close="isScannerOpen = false" 
            @scan="handleScan"
        />
    </div>
</template>
