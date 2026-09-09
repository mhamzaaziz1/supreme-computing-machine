<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    item: Object,
    index: Number
});

const emit = defineEmits(['update-qty', 'remove']);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const openItemModifier = () => {
    alert('Item Modifier Modal will open here');
};
</script>

<template>
    <div class="flex justify-between items-start p-3 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors group">
        <div class="flex-1 pr-3">
            <div class="flex justify-between">
                <h4 class="font-bold text-gray-800 dark:text-gray-100 text-sm leading-tight cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" @click="openItemModifier" title="Edit Item">
                    {{ item.name }}
                </h4>
                <button @click="emit('remove', index)" class="text-gray-400 hover:text-red-500 transition-colors bg-gray-50 dark:bg-slate-700 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md p-1" title="Remove">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ item.variation }}</p>
            
            <div class="flex items-center mt-2 justify-between">
                <div class="flex items-center bg-gray-100 dark:bg-slate-700 rounded-lg p-0.5 border border-gray-200 dark:border-slate-600">
                    <button @click="emit('update-qty', index, item.quantity - 1)" class="w-7 h-7 bg-white dark:bg-slate-800 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 flex items-center justify-center shadow-sm transition-colors font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                    </button>
                    
                    <input 
                        type="number" 
                        :value="item.quantity"
                        @change="e => emit('update-qty', index, parseFloat(e.target.value) || 1)"
                        class="w-10 bg-transparent border-none text-center text-sm font-bold text-gray-800 dark:text-gray-200 focus:ring-0 p-0"
                    />
                    
                    <button @click="emit('update-qty', index, item.quantity + 1)" class="w-7 h-7 bg-white dark:bg-slate-800 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 flex items-center justify-center shadow-sm transition-colors font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
                
                <div class="text-right">
                    <div class="font-extrabold text-gray-900 dark:text-white text-sm">{{ formatCurrency(item.quantity * item.unit_price) }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ formatCurrency(item.unit_price) }}/unit</div>
                </div>
            </div>
        </div>
    </div>
</template>
