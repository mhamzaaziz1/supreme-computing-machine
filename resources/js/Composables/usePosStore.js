import { reactive, computed } from 'vue';

const state = reactive({
    cart: [],
    customer: {
        id: null,
        name: 'Walk-in Customer',
        balance: 0
    },
    locationId: null,
    discount: {
        type: 'fixed',
        amount: 0
    },
    tax: {
        id: null,
        rate: 0
    },
    shipping: {
        amount: 0
    },
    payment: {
        method: 'cash',
        amount: 0
    }
});

export function usePosStore() {
    const addToCart = (product) => {
        const existingItem = state.cart.find(item => item.variation_id === product.variation_id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            state.cart.push({
                ...product,
                quantity: 1,
                unit_price: parseFloat(product.selling_price || 0),
                line_discount_type: 'fixed',
                line_discount_amount: 0,
                note: ''
            });
        }
    };

    const removeFromCart = (index) => {
        state.cart.splice(index, 1);
    };

    const updateQuantity = (index, qty) => {
        if (qty <= 0) {
            removeFromCart(index);
        } else {
            state.cart[index].quantity = qty;
        }
    };

    const clearCart = () => {
        state.cart = [];
    };

    const subtotal = computed(() => {
        return state.cart.reduce((sum, item) => {
            let itemTotal = item.quantity * item.unit_price;
            if (item.line_discount_type === 'fixed') {
                itemTotal -= item.line_discount_amount;
            } else if (item.line_discount_type === 'percentage') {
                itemTotal -= itemTotal * (item.line_discount_amount / 100);
            }
            return sum + itemTotal;
        }, 0);
    });

    const totalDiscount = computed(() => {
        if (state.discount.type === 'fixed') {
            return parseFloat(state.discount.amount || 0);
        }
        return subtotal.value * (parseFloat(state.discount.amount || 0) / 100);
    });

    const totalTax = computed(() => {
        const taxableAmount = subtotal.value - totalDiscount.value;
        return taxableAmount * (parseFloat(state.tax.rate || 0) / 100);
    });

    const totalPayable = computed(() => {
        return subtotal.value - totalDiscount.value + totalTax.value + parseFloat(state.shipping.amount || 0);
    });

    return {
        state,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        subtotal,
        totalDiscount,
        totalTax,
        totalPayable
    };
}
