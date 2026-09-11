<script setup>
import { usePosStore } from '../../Composables/usePosStore';

const { clearCart } = usePosStore();

const payCash = () => {
    alert('Quick Cash Payment processed');
};

const payMultiple = () => {
    alert('Multiple Payment Modal will open here');
};

const creditSale = () => {
    alert('Credit Sale processed');
};

const suspendSale = () => {
    alert('Suspend Modal will open here');
};
</script>

<template>
    <div class="p-3 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 transition-colors duration-300">
        <div class="grid grid-cols-4 gap-2 mb-2">
            <button @click="suspendSale" class="col-span-1 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm flex flex-col items-center justify-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Suspend
            </button>
            <button @click="creditSale" class="col-span-1 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm flex flex-col items-center justify-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Credit
            </button>
            <button @click="payMultiple" class="col-span-2 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                Multiple Pay
            </button>
        </div>
        
        <div class="grid grid-cols-3 gap-2">
            <button @click="clearCart" class="col-span-1 py-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2 border border-red-200 dark:border-red-800 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Cancel
            </button>
            <button @click="payCash" class="col-span-2 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold rounded-xl shadow-md transition-transform transform active:scale-95 text-lg flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Cash
            </button>
        </div>
    </div>
</template>
