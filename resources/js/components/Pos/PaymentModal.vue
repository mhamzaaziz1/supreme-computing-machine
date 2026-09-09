<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { usePosStore } from '../../Composables/usePosStore';

const props = defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close', 'process']);

const { state, totalPayable } = usePosStore();

const paymentMethods = [
    { id: 'cash', name: 'Cash', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' },
    { id: 'card', name: 'Card', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
    { id: 'bank_transfer', name: 'Bank Transfer', icon: 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z' },
    { id: 'cheque', name: 'Cheque', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
];

const selectedMethod = ref('cash');
const amountPaid = ref(totalPayable.value);
const paymentNote = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const handleProcess = () => {
    emit('process', {
        method: selectedMethod.value,
        amount: amountPaid.value,
        note: paymentNote.value
    });
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-slate-700">
                
                <!-- Header -->
                <div class="bg-white dark:bg-slate-800 px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white" id="modal-title">
                        Complete Payment
                    </h3>
                    <button @click="emit('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 rounded-full p-2 transition-colors">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-6 bg-gray-50 dark:bg-slate-900/50 space-y-6">
                    
                    <!-- Total Payable -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl p-6 text-center border border-indigo-100 dark:border-indigo-800/30">
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Total Payable</p>
                        <p class="text-4xl font-extrabold text-indigo-700 dark:text-indigo-300">{{ formatCurrency(totalPayable) }}</p>
                    </div>

                    <!-- Payment Methods -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <button 
                                v-for="method in paymentMethods" 
                                :key="method.id"
                                @click="selectedMethod = method.id"
                                :class="{'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800': selectedMethod === method.id, 'bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700': selectedMethod !== method.id}"
                                class="flex flex-col items-center justify-center p-4 border rounded-xl transition-all shadow-sm focus:outline-none"
                            >
                                <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="method.icon"></path>
                                </svg>
                                <span class="text-sm font-medium">{{ method.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Amount & Note -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Amount Paid</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input 
                                    type="number" 
                                    v-model="amountPaid"
                                    class="block w-full pl-7 pr-3 py-3 border border-gray-300 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-lg font-bold transition-all shadow-sm" 
                                />
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Payment Note (Optional)</label>
                            <input 
                                type="text" 
                                v-model="paymentNote"
                                class="block w-full px-3 py-3 border border-gray-300 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm" 
                                placeholder="E.g. Paid in full" 
                            />
                        </div>
                    </div>

                    <!-- Change Return (if amount > totalPayable) -->
                    <div v-if="amountPaid > totalPayable" class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800/30 flex justify-between items-center">
                        <span class="font-medium text-amber-800 dark:text-amber-400 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Change Return
                        </span>
                        <span class="font-bold text-lg text-amber-700 dark:text-amber-500">{{ formatCurrency(amountPaid - totalPayable) }}</span>
                    </div>

                </div>

                <!-- Footer -->
                <div class="bg-white dark:bg-slate-800 px-6 py-5 border-t border-gray-100 dark:border-slate-700 sm:flex sm:flex-row-reverse">
                    <button @click="handleProcess" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-md px-6 py-3 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-all transform active:scale-95">
                        Confirm Payment
                    </button>
                    <button @click="emit('close')" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-slate-600 shadow-sm px-6 py-3 bg-white dark:bg-slate-700 text-base font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>
