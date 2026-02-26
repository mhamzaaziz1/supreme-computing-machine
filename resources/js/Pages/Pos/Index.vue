<script setup>
import { defineProps, ref, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    locations: Object,
    default_location: Object,
    categories: [Array, Boolean],
    brands: [Array, Boolean],
    user: Object,
});

const products = ref([]);
const isLoadingPromise = ref(false);
const selectedLocation = ref(props.default_location ? props.default_location.id : '');
const selectedCategory = ref('');
const searchTerm = ref('');

const fetchProducts = async () => {
    isLoadingPromise.value = true;
    try {
        const response = await axios.get(route('pos.v2.products'), {
            params: {
                location_id: selectedLocation.value,
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

onMounted(() => {
    fetchProducts();
});

const cart = ref([]);

const addToCart = (product) => {
    const existingItem = cart.value.find(item => item.variation_id === product.variation_id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.value.push({
            ...product,
            quantity: 1,
            unit_price: parseFloat(product.selling_price) 
        });
    }
};

const removeFromCart = (index) => {
    cart.value.splice(index, 1);
};

const updateQuantity = (index, qty) => {
    if (qty <= 0) {
        removeFromCart(index);
    } else {
        cart.value[index].quantity = qty;
    }
};

import { computed } from 'vue';

const subtotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
});

const tax = computed(() => subtotal.value * 0.0); // Placeholder 0% tax
const total = computed(() => subtotal.value + tax.value);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};
</script>

<template>
    <Head title="POS V2" />

    <div class="flex h-screen bg-gray-100 font-sans">
        <!-- Sidebar / Navigation (Optional, or part of Header) -->

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 p-4 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold text-gray-800">POS V2</h1>
                    <div>
                        <label class="text-sm text-gray-500 mr-2">Location:</label>
                        <select v-model="selectedLocation" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                             <option v-for="(name, id) in locations" :key="id" :value="id">
                                {{ name }}
                             </option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, {{ user.first_name }}</span>
                    <a href="/home" class="text-indigo-600 hover:text-indigo-800 text-sm">Dashboard</a>
                </div>
            </header>

            <!-- POS Workspace -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Left Panel: Products -->
                <div class="flex-1 bg-gray-50 p-4 overflow-y-auto">
                    <!-- Filters -->
                    <div class="mb-4 flex space-x-2 overflow-x-auto pb-2">
                        <button 
                            @click="selectedCategory = ''; fetchProducts()"
                            :class="{'bg-indigo-600 text-white': selectedCategory === '', 'bg-white text-gray-700': selectedCategory !== ''}"
                            class="px-4 py-2 rounded-lg shadow whitespace-nowrap"
                        >
                            All Categories
                        </button>
                        <template v-if="categories">
                            <button 
                                v-for="cat in categories" 
                                :key="cat.id" 
                                @click="selectedCategory = cat.id; fetchProducts()"
                                :class="{'bg-indigo-600 text-white': selectedCategory === cat.id, 'bg-white text-gray-700': selectedCategory !== cat.id}"
                                class="px-4 py-2 rounded-lg shadow hover:bg-gray-100 whitespace-nowrap"
                            >
                                {{ cat.name }}
                            </button>
                        </template>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoadingPromise" class="flex justify-center items-center h-64">
                         <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>

                    <!-- Product Grid -->
                    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div v-for="product in products" :key="product.variation_id" class="bg-white rounded-lg shadow p-4 flex flex-col items-center cursor-pointer hover:shadow-md transition relative group">
                            <div class="h-24 w-full bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-400 overflow-hidden">
                                <img v-if="product.product_image" :src="'/uploads/img/' + product.product_image" class="h-full object-cover" alt="Product Image">
                                <span v-else>No Image</span>
                            </div>
                            <h3 class="font-medium text-gray-800 text-center leading-tight mb-1">{{ product.name }}</h3>
                             <p class="text-xs text-gray-500 mb-1">{{ product.variation }}</p>
                            <p class="text-green-600 font-bold mt-auto">{{ product.selling_price }}</p>
                            
                            <!-- Add to cart overlay -->
                             <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center">
                                 <button @click.stop="addToCart(product)" class="opacity-0 group-hover:opacity-100 bg-indigo-600 text-white px-4 py-2 rounded shadow transform translate-y-2 group-hover:translate-y-0 transition-all">
                                     Add to Cart
                                 </button>
                             </div>
                        </div>
                    </div>
                    
                     <div v-if="!isLoadingPromise && products.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-500">
                        <p>No products found.</p>
                    </div>
                </div>

                <!-- Right Panel: Cart -->
                <div class="w-96 bg-white shadow-xl flex flex-col h-full border-l border-gray-200">
                    <!-- Customer Selection -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="font-semibold text-gray-700">Current Sale</h2>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Walk-in Customer</span>
                        </div>
                        <input type="text" placeholder="Search customer..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Cart Items -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <div v-if="cart.length === 0" class="text-center text-gray-500 mt-10">
                            Cart is empty
                        </div>
                        <div v-for="(item, index) in cart" :key="item.variation_id" class="flex justify-between items-start border-b pb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 text-sm">{{ item.name }}</h4>
                                <p class="text-xs text-gray-500">{{ item.variation }}</p>
                                <div class="flex items-center mt-1">
                                    <button @click="updateQuantity(index, item.quantity - 1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 flex items-center justify-center">-</button>
                                    <span class="mx-2 text-sm">{{ item.quantity }}</span>
                                    <button @click="updateQuantity(index, item.quantity + 1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 flex items-center justify-center">+</button>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800 text-sm">{{ formatCurrency(item.quantity * item.unit_price) }}</div>
                                <button @click="removeFromCart(index)" class="text-xs text-red-500 hover:underline mt-1">Remove</button>
                            </div>
                        </div>
                    </div>

                    <!-- Totals & Actions -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between mb-2 text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-800">{{ formatCurrency(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between mb-2 text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium text-gray-800">{{ formatCurrency(tax) }}</span>
                        </div>
                        <div class="flex justify-between mb-4 text-xl font-bold text-gray-900">
                            <span>Total</span>
                            <span>{{ formatCurrency(total) }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button @click="cart = []" class="bg-red-500 hover:bg-red-600 text-white py-3 rounded shadow font-medium">Cancel</button>
                            <button class="bg-green-600 hover:bg-green-700 text-white py-3 rounded shadow font-medium">Pay Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
