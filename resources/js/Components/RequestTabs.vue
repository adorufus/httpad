<template>
    <div>
        <div class="mb-4">
            <button @click="selectTab('body')" :class="tabClass('body')" class="mr-2">
                Body
            </button>
            <button @click="selectTab('headers')" :class="tabClass('headers')">
                Headers
            </button>
        </div>
        <div v-if="selectedTab === 'body'">
            <pre class="bg-gray-100 p-4 rounded">{{ formattedBody }}</pre>
        </div>
        <div v-if="selectedTab === 'headers'">
            <pre class="bg-gray-100 p-4 rounded">{{ formattedHeaders }}</pre>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        body: {
            type: Object,
            required: true,
            default: () => ({})
        },
        headers: {
            type: Object,
            required: true,
            default: () => ({})
        }
    },
    data() {
        return {
            selectedTab: 'body'
        };
    },
    computed: {
        formattedBody() {
            return JSON.stringify(this.body, null, 2);
        },
        formattedHeaders() {
            return JSON.stringify(this.headers, null, 2);
        }
    },
    methods: {
        selectTab(tab) {
            this.selectedTab = tab;
        },
        tabClass(tab) {
            return this.selectedTab === tab
                ? 'bg-blue-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline'
                : 'bg-gray-300 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline';
        }
    }
};
</script>

<style scoped>
button {
    transition: background-color 0.3s ease;
}
</style>
