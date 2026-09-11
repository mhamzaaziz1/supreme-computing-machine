<script setup>
import { defineProps, ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { usePosStore } from '../../Composables/usePosStore';

import PosHeader from '../../Components/Pos/PosHeader.vue';
import CustomerSelector from '../../Components/Pos/CustomerSelector.vue';
import ProductSearch from '../../Components/Pos/ProductSearch.vue';
import CartItem from '../../Components/Pos/CartItem.vue';
import CartTotals from '../../Components/Pos/CartTotals.vue';
import ActionButtons from '../../Components/Pos/ActionButtons.vue';

const props = defineProps({
    locations: Object,
    default_location: Object,
    categories: [Array, Boolean],
    brands: [Array, Boolean],
    user: Object,
});

const { state, addToCart, updateQuantity, removeFromCart } = usePosStore();

const products = ref([]);
const isLoadingPromise = ref(false);
const selectedCategory = ref('');
const searchTerm = ref('');

onMounted(() => {
    if (props.default_location) {
        state.locationId = props.default_location.id;
    }
    fetchProducts();
});

const fetchProducts = async () => {
    isLoadingPromise.value = true;
    try {
        const response = await axios.get('/pos/v2/products', {
            params: {
                location_id: state.locationId,
                category_id: selectedCategory.value,
                term: searchTerm.value,
            }
        });
        products.value = response.data.data;
    } catch (error) {
        console.error("Error fetching products:", error);
    } finally {
        isLoadingPromise.value = false;
    }
};

const handleSearch = (term) => {
    searchTerm.value = term;
    fetchProducts();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};
</script>

<template>
    <Head title="Premium POS V2" />

    <div class="flex flex-col h-screen bg-gray-100 dark:bg-slate-900 font-sans transition-colors duration-300 overflow-hidden">
        
        <PosHeader :locations="locations" :user="user" />

        <!-- Main POS Workspace -->
        <div class="flex flex-1 overflow-hidden">
            
            <!-- Left Pane: Product Discovery -->
            <div class="flex-1 flex flex-col bg-gray-50/50 dark:bg-slate-900/50 overflow-hidden relative">
                
                <ProductSearch @search="handleSearch" />
                
                <!-- Category Filters -->
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md z-10 sticky top-0 overflow-x-auto whitespace-nowrap hide-scrollbar flex space-x-2 transition-colors">
                    <button 
                        @click="selectedCategory = ''; fetchProducts()"
                        :class="{'bg-indigo-600 text-white shadow-md border-indigo-600': selectedCategory === '', 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700': selectedCategory !== ''}"
                        class="px-5 py-2 rounded-full border text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-slate-900"
                    >
                        All Categories
                    </button>
                    <template v-if="categories">
                        <button 
                            v-for="cat in categories" 
                            :key="cat.id" 
                            @click="selectedCategory = cat.id; fetchProducts()"
                            :class="{'bg-indigo-600 text-white shadow-md border-indigo-600': selectedCategory === cat.id, 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700': selectedCategory !== cat.id}"
                            class="px-5 py-2 rounded-full border text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-slate-900"
                        >
                            {{ cat.name }}
                        </button>
                    </template>
                </div>

                <!-- Product Grid Area -->
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    
                    <!-- Loading Skeleton -->
                    <div v-if="isLoadingPromise" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                         <div v-for="i in 12" :key="i" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-4 animate-pulse">
                             <div class="h-32 bg-gray-200 dark:bg-slate-700 rounded-xl mb-3"></div>
                             <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded w-3/4 mb-2"></div>
                             <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded w-1/2 mb-4"></div>
                             <div class="h-5 bg-gray-200 dark:bg-slate-700 rounded w-1/3 mt-auto"></div>
                         </div>
                    </div>

                    <!-- Products -->
                    <div v-else-if="products.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4">
                        <div 
                            v-for="product in products" 
                            :key="product.variation_id" 
                            @click="addToCart(product)"
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-3 flex flex-col items-center cursor-pointer hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden"
                        >
                            <div class="h-32 w-full bg-gray-50 dark:bg-slate-700/50 rounded-xl mb-3 flex items-center justify-center text-gray-400 overflow-hidden relative">
                                <img v-if="product.product_image" :src="'/uploads/img/' + product.product_image" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Product Image">
                                <div v-else class="text-xs">No Image</div>
                                
                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-indigo-600/10 dark:bg-indigo-400/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div class="bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 rounded-full p-2 shadow-lg transform scale-50 opacity-0 group-hover:scale-100 group-hover:opacity-100 transition-all duration-300 delay-75">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-full text-left flex-1 flex flex-col">
                                <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm leading-tight mb-1 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ product.name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ product.variation }}</p>
                                <p class="text-indigo-600 dark:text-indigo-400 font-extrabold mt-auto text-base">{{ formatCurrency(product.selling_price) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500 pb-20">
                        <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <p class="text-lg font-medium">No products found</p>
                        <p class="text-sm mt-1">Try adjusting your filters or search term</p>
                    </div>
                </div>
            </div>

            <!-- Right Pane: Cart & Checkout -->
            <div class="w-[420px] bg-white dark:bg-slate-900 flex flex-col h-full shadow-2xl border-l border-gray-200 dark:border-slate-800 z-20 transition-colors duration-300">
                
                <CustomerSelector />

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/30 dark:bg-slate-900/30 custom-scrollbar">
                    
                    <div v-if="state.cart.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500 opacity-70">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="font-medium text-lg">Your cart is empty</p>
                        <p class="text-sm mt-1 text-center">Scan a barcode or add items from the grid</p>
                    </div>
                    
                    <transition-group name="list" tag="div" class="space-y-3">
                        <CartItem 
                            v-for="(item, index) in state.cart" 
                            :key="item.variation_id" 
                            :item="item" 
                            :index="index"
                            @update-qty="updateQuantity"
                            @remove="removeFromCart"
                        />
                    </transition-group>
                </div>

                <!-- Footer Checkout Area -->
                <div class="flex-shrink-0 bg-white dark:bg-slate-900 transition-colors duration-300">
                    <CartTotals />
                    <ActionButtons />
                </div>
            </div>
            
        </div>
    </div>
</template>

<style>
/* Custom Scrollbar for modern look */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* List Transitions */
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
