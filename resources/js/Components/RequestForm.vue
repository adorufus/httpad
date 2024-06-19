<template>
    <div class="max-w-2xl mx-auto py-10">
        <form @submit.prevent="sendRequest" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Method</label>
                <select v-model="method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>DELETE</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">URL</label>
                <input v-model="url" type="text" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Headers</label>
                <div v-for="(header, index) in headers" :key="index" class="flex mb-2">
                    <input v-model="header.key" type="text" placeholder="Header Key" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2" />
                    <input v-model="header.value" type="text" placeholder="Header Value" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                    <button @click.prevent="removeHeader(index)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Remove</button>
                </div>
                <button @click.prevent="addHeader" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Header</button>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Body Type</label>
                <select v-model="bodyType" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="json">JSON</option>
                    <option value="form">Form Data</option>
                </select>
            </div>
            <div v-if="bodyType === 'json'" class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Body (JSON)</label>
                <textarea v-model="body" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div v-if="bodyType === 'form'" class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Form Data</label>
                <div v-for="(formField, index) in formFields" :key="index" class="flex mb-2 items-center">
                    <input v-model="formField.key" type="text" placeholder="Field Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2" />
                    <select v-model="formField.type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2">
                        <option value="text">Text</option>
                        <option value="file">File</option>
                    </select>
                    <input v-if="formField.type === 'text'" v-model="formField.value" type="text" placeholder="Field Value" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                    <input v-if="formField.type === 'file'" type="file" @change="handleFileUpload($event, index)" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                    <button @click.prevent="removeFormField(index)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Remove</button>
                </div>
                <button @click.prevent="addFormField" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Field</button>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Send
                </button>
                <button @click.prevent="saveRequest" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save
                </button>
                <button @click.prevent="loadRequest" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Load
                </button>
            </div>
        </form>

        <div v-if="response">
            <h3 class="text-xl font-bold mb-2">Request Details</h3>
            <div @click="toggleRequestDetails" class="cursor-pointer bg-gray-200 p-4 rounded mb-4">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-700">{{ method }} {{ url }}</span>
                    <span>{{ isRequestDetailsOpen ? '-' : '+' }}</span>
                </div>
            </div>
            <transition name="fade">
                <div v-if="isRequestDetailsOpen" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <h4 class="text-lg font-bold mb-2">Headers</h4>
                    <pre class="bg-gray-100 p-4 rounded">{{ formattedHeaders }}</pre>
                    <h4 class="text-lg font-bold mb-2">Body</h4>
                    <pre class="bg-gray-100 p-4 rounded">{{ formattedBody }}</pre>
                </div>
            </transition>

            <h3 class="text-xl font-bold mb-2">Response</h3>
            <RequestTabs :body="response.data || {}" :headers="response.headers || {}"></RequestTabs>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import RequestTabs from "@/Components/RequestTabs.vue";
import HistorySideBar from "@/Components/HistorySideBar.vue";

export default {
    components: {
        HistorySideBar,
        RequestTabs
    },
    data() {
        return {
            method: 'GET',
            url: '',
            headers: [{ key: '', value: '' }],
            bodyType: 'json',
            body: '',
            formFields: [{ key: '', value: '', type: 'text' }],
            response: null,
            isRequestDetailsOpen: false,
        };
    },
    computed: {
        formattedHeaders() {
            return JSON.stringify(this.headers.reduce((acc, header) => {
                if (header.key && header.value) {
                    acc[header.key] = header.value;
                }
                return acc;
            }, {}), null, 2);
        },
        formattedBody() {
            if (this.bodyType === 'json') {
                return this.body ? JSON.stringify(JSON.parse(this.body), null, 2) : '{}';
            } else {
                return this.formFields.reduce((acc, field) => {
                    acc[field.key] = field.type === 'file' ? '[File]' : field.value;
                    return acc;
                }, {});
            }
        }
    },
    methods: {
        async sendRequest() {
            try {
                const headers = this.headers.reduce((acc, header) => {
                    if (header.key && header.value) {
                        acc[header.key] = header.value;
                    }
                    return acc;
                }, {});

                let body;
                let contentType = 'application/json';

                if (this.bodyType === 'json') {
                    body = this.body ? JSON.parse(this.body) : {};
                } else {
                    body = new FormData();
                    this.formFields.forEach(field => {
                        if (field.key) {
                            if (field.type === 'text') {
                                body.append(field.key, field.value);
                            } else if (field.type === 'file' && field.file) {
                                body.append(field.key, field.file);
                            }
                        }
                    });
                    contentType = 'multipart/form-data';
                }

                const config = {
                    method: this.method,
                    url: this.url,
                    headers: {
                        ...headers,
                        'Content-Type': contentType,
                    },
                    data: body,
                };

                const res = await axios(config);
                this.response = res;
            } catch (error) {
                console.error(error);
            }
        },
        async saveRequest() {
            const request = {
                method: this.method,
                url: this.url,
                headers: this.headers,
                bodyType: this.bodyType,
                body: this.body,
                formFields: this.formFields,
            };
            localStorage.setItem('savedRequest', JSON.stringify(request));
            alert('Request saved');
        },
        loadRequest() {
            const savedRequest = JSON.parse(localStorage.getItem('savedRequest'));
            if (savedRequest) {
                this.method = savedRequest.method;
                this.url = savedRequest.url;
                this.headers = savedRequest.headers;
                this.bodyType = savedRequest.bodyType;
                this.body = savedRequest.body;
                this.formFields = savedRequest.formFields;
            } else {
                alert('No saved request found');
            }
        },
        addHeader() {
            this.headers.push({ key: '', value: '' });
        },
        removeHeader(index) {
            this.headers.splice(index, 1);
        },
        addFormField() {
            this.formFields.push({ key: '', value: '', type: 'text' });
        },
        removeFormField(index) {
            this.formFields.splice(index, 1);
        },
        handleFileUpload(event, index) {
            const file = event.target.files[0];
            this.formFields[index].file = file;
        },
        toggleRequestDetails() {
            this.isRequestDetailsOpen = !this.isRequestDetailsOpen;
        }
    }
};
</script>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>
