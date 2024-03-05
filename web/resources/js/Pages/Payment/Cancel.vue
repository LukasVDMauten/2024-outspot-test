<template>
    <body class="flex justify-center items-center w-screen h-screen">
    <div class="bg-white p-6 rounded shadow-md flex flex-col items-center space-y-4 max-w-md">
        <h2 class="text-2xl font-bold text-gray-800">Payment Canceled</h2>
        <p class="text-gray-600">Your payment process was not completed.</p>
        <p class="text-gray-600">If you have any questions or concerns, please contact our support team.</p>
        <button
            @click="goBack"
            class="bg-black text-white px-4 py-2 rounded hover:bg-gray-700"
        >
            Try Again
        </button>
    </div>
    </body>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    methods: {
        goBack() {
            // Navigate the user back to the payment page or home page
            Inertia.visit('/');
        }
    }
};
</script>
