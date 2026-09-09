<script setup>
import { ref, defineProps, defineEmits, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close']);
const transactions = ref([]);
const isLoading = ref(false);

const fetchTransactions = async () => {
    isLoading.value = true;
    try {
        // In a real implementation, we would call the actual API endpoint
        // const response = await axios.get('/sells/pos/get-recent-transactions');
        // transactions.value = response.data;
        
        // Mock data for demo
        setTimeout(() => {
            transactions.value = [
                { id: 1, invoice_no: 'INV-0001', customer: 'Walk-in Customer', total: 150.00, status: 'Final', time: '10 mins ago' },
                { id: 2, invoice_no: 'INV-0002', customer: 'John Doe', total: 450.50, status: 'Final', time: '1 hour ago' },
                { id: 3, invoice_no: 'SUS-0001', customer: 'Jane Smith', total: 85.00, status: 'Suspended', time: '2 hours ago' },
            ];
            isLoading.value = false;
        }, 800);
    } catch (error) {
        console.error("Error fetching recent transactions:", error);
        isLoading.value = false;
    }
};

onMounted(() => {
    if (props.isOpen) {
        fetchTransactions();
    }
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const printInvoice = (id) => {
    alert('Printing invoice ' + id);
};

const editTransaction = (id) => {
    alert('Editing transaction ' + id);
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-gray-100 dark:border-slate-700">
                
                <div class="bg-white dark:bg-slate-800 px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white flex items-center" id="modal-title">
                        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Recent Transactions
                    </h3>
                    <button @click="emit('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 rounded-full p-2 transition-colors">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-gray-50 dark:bg-slate-900/50 p-0">
                    
                    <div v-if="isLoading" class="p-12 flex justify-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div v-else-if="transactions.length === 0" class="p-12 text-center text-gray-500">
                        No recent transactions found.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-100 dark:bg-slate-800/80">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice No.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-800">
                                <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ tx.invoice_no }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                        {{ tx.customer }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ tx.time }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': tx.status === 'Final', 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400': tx.status === 'Suspended'}" class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-md">
                                            {{ tx.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-gray-900 dark:text-white text-right">
                                        {{ formatCurrency(tx.total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editTransaction(tx.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3" title="Edit">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="printInvoice(tx.id)" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200" title="Print">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
