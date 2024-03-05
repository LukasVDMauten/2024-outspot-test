<template>
    <body class="flex justify-center items-center w-screen h-screen">
        <div class="flex items-center space-x-2 max-w-lg w-full">
            <div class="relative w-full">
                <input
                    type="number"
                    v-model.number="amount"
                    @input="validateAmount"
                    min=10
                    max=100
                    placeholder="Amount"
                    class="border border-gray-300 rounded px-4 py-2 text-gray-700 focus:outline-none focus:border-blue-500 w-full"
                />
                <div class="absolute bottom-0 transform translate-y-full">
                    <p class="text-xs text-gray-600">
                        Enter an amount between 10 and 100 EUR
                    </p>
                    <p v-if="error" class="text-xs text-red-500">{{ errorMessage }}</p>
                </div>

            </div>
            <button
                :disabled="!isValid"
                @click="submitPayment"
                class="bg-black text-white px-4 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
            >
                Pay
            </button>
        </div>
    </body>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    title: 'Home',
    name: 'Home',
    data() {
        return {
            amount: '',
            isValid: false,
            error: false,
            errorMessage: ''
        };
    },
    methods: {
        validateAmount() {
            this.isValid = this.amount >= 10 && this.amount <= 100;
        },
        submitPayment() {
            if (this.isValid) {
                // Submit the payment
                this.error = false;

                Inertia.post('/create-payment', { amount: this.amount }, {
                    onSuccess: () => {
                        console.log('Payment submitted:', this.amount);
                    },
                    onError: (errors) => {
                        this.error = true;
                        if (errors.amount) {
                            this.errorMessage = errors.amount;
                        } else {
                            this.errorMessage = 'An unexpected error occurred.';
                        }
                    }
                });
            }
        }
    }
};
</script>

<style scoped>

</style>
