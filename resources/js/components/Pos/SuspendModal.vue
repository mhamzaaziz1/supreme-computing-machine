<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close', 'suspend']);

const suspendNote = ref('');

const handleSuspend = () => {
    emit('suspend', suspendNote.value);
    suspendNote.value = '';
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-slate-700">
                
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-white flex items-center" id="modal-title">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Suspend Sale
                    </h3>
                    <button @click="emit('close')" class="text-white hover:text-amber-100 focus:outline-none bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6 bg-gray-50 dark:bg-slate-900/50">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Suspending this sale will save the current cart and clear the workspace for the next customer. You can resume this sale later from the suspended sales list.
                    </p>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Note</label>
                        <textarea 
                            v-model="suspendNote"
                            rows="3"
                            class="block w-full px-3 py-3 border border-gray-300 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 sm:text-sm transition-all shadow-sm" 
                            placeholder="E.g. Waiting for customer to grab wallet..." 
                        ></textarea>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 px-6 py-5 border-t border-gray-100 dark:border-slate-700 sm:flex sm:flex-row-reverse">
                    <button @click="handleSuspend" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-md px-6 py-3 bg-amber-500 text-base font-bold text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm transition-all transform active:scale-95">
                        Suspend
                    </button>
                    <button @click="emit('close')" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-slate-600 shadow-sm px-6 py-3 bg-white dark:bg-slate-700 text-base font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
