<script setup>
import { usePosStore } from '../../Composables/usePosStore';

const { state, subtotal, totalDiscount, totalTax, totalPayable } = usePosStore();

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const openDiscountModal = () => {
    alert('Discount modal will open here');
};

const openTaxModal = () => {
    alert('Tax modal will open here');
};

const openShippingModal = () => {
    alert('Shipping modal will open here');
};
</script>

<template>
    <div class="bg-gray-50 dark:bg-slate-800 p-4 border-t border-gray-200 dark:border-slate-800 transition-colors duration-300 shadow-inner">
        <div class="space-y-3">
            <!-- Subtotal -->
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-gray-400 font-medium">Items ({{ state.cart.length }})</span>
                <span class="text-gray-800 dark:text-gray-200 font-bold">{{ formatCurrency(subtotal) }}</span>
            </div>
            
            <!-- Discount -->
            <div class="flex justify-between items-center text-sm group cursor-pointer" @click="openDiscountModal">
                <div class="flex items-center text-gray-600 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    <span class="font-medium border-b border-dashed border-gray-400 dark:border-gray-600 group-hover:border-indigo-400">Discount</span>
                    <svg class="w-3.5 h-3.5 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </div>
                <span class="text-red-500 font-bold">- {{ formatCurrency(totalDiscount) }}</span>
            </div>
            
            <!-- Tax -->
            <div class="flex justify-between items-center text-sm group cursor-pointer" @click="openTaxModal">
                <div class="flex items-center text-gray-600 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    <span class="font-medium border-b border-dashed border-gray-400 dark:border-gray-600 group-hover:border-indigo-400">Order Tax</span>
                    <svg class="w-3.5 h-3.5 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </div>
                <span class="text-gray-800 dark:text-gray-200 font-bold">+ {{ formatCurrency(totalTax) }}</span>
            </div>
            
            <!-- Shipping -->
            <div class="flex justify-between items-center text-sm group cursor-pointer" @click="openShippingModal">
                <div class="flex items-center text-gray-600 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    <span class="font-medium border-b border-dashed border-gray-400 dark:border-gray-600 group-hover:border-indigo-400">Shipping</span>
                    <svg class="w-3.5 h-3.5 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </div>
                <span class="text-gray-800 dark:text-gray-200 font-bold">+ {{ formatCurrency(state.shipping.amount) }}</span>
            </div>
        </div>
        
        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-slate-700">
            <div class="flex justify-between items-end">
                <span class="text-gray-800 dark:text-gray-200 font-semibold text-lg">Total Payable</span>
                <span class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 tracking-tight">{{ formatCurrency(totalPayable) }}</span>
            </div>
        </div>
    </div>
</template>
