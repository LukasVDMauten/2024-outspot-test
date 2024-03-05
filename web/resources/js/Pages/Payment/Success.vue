<template>
    <body class="flex justify-center items-center w-screen h-screen bg-green-100">
    <div class="text-center">
        <h1 class="text-3xl text-green-800 font-bold">
            Payment Successful!
        </h1>
        <p class="text-xl text-green-600 mt-4">
            Your payment has a status of {{ status }}.
        </p>
        <div class="mt-6">
            <button
                @click="goHome"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700"
            >
                Go to Homepage
            </button>
        </div>
    </div>
    </body>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    props: {
        status: String
    },
    methods: {
        goHome() {
            Inertia.visit('/');
        }
    }
};
</script>
