<script setup>
import { usePosStore } from '../../Composables/usePosStore';

const { state } = usePosStore();

const openCustomerModal = () => {
    // In a real implementation, this would open a modal to add/select a customer
    alert('Customer modal will open here');
};
</script>

<template>
    <div class="p-4 border-b border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Customer
            </h2>
            <button @click="openCustomerModal" class="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors" title="Add Customer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </button>
        </div>
        
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input 
                type="text" 
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl leading-5 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm" 
                placeholder="Search customer by name or phone..." 
            />
        </div>
        
        <!-- Active Customer Info -->
        <div class="mt-3 bg-gray-50 dark:bg-slate-800 rounded-lg p-3 border border-gray-100 dark:border-slate-700 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ state.customer.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Balance: {{ state.customer.balance }}</p>
            </div>
            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 text-xs font-bold rounded-md">
                Active
            </span>
        </div>
    </div>
</template>
