<template>
    <div class="bg-gray-100 w-64 h-full p-4">
        <h2 class="text-lg font-bold mb-4">Request History</h2>
        <ul>
            <li v-for="request in requests" :key="request.id" class="mb-2">
                <button @click="loadRequest(request)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    {{ request.method }} {{ request.url }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        requests: {
            type: Array,
            required: true
        }
    },
    methods: {
        loadRequest(request) {
            this.$emit('load-request', request);
        }
    }
};
</script>
