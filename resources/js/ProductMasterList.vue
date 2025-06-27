<template>
    <div class="product-list">
        <h1>Product Master List</h1>

        <div class="search-upload">
            <input
                v-model="searchProductId"
                type="text"
                placeholder="Search by Product ID"
                @input="fetchProducts(1)"
            />

            <input type="file" @change="handleFileUpload"/>
            <button @click="uploadFile" :disabled="!selectedFile">Upload</button>
        </div>

        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Product ID</th>
                <th>Type</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Capacity</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in products.data" :key="product.product_id">
                <td>{{ product.product_id }}</td>
                <td>{{ product.type }}</td>
                <td>{{ product.brand }}</td>
                <td>{{ product.model }}</td>
                <td>{{ product.capacity }}</td>
                <td>{{ product.quantity }}</td>
            </tr>
            </tbody>
        </table>

        <div class="pagination" v-if="products && products.data.length">
            <button
                @click="fetchProducts(products.current_page - 1)"
                :disabled="products.current_page === 1"
            >
                &laquo; Prev
            </button>

            <template v-for="page in products.last_page" :key="page">
                <button
                    @click="fetchProducts(page)"
                    :class="{ active: page === products.current_page }"
                >
                    {{ page }}
                </button>
            </template>


            <button
                @click="fetchProducts(products.current_page + 1)"
                :disabled="products.current_page === products.last_page"
            >
                Next &raquo;
            </button>
        </div>

    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import axios from 'axios'
import {useToast} from 'vue-toastification'

const products = ref({data: [], meta: null});
const searchProductId = ref('');
const selectedFile = ref(null);
const toast = useToast();

const fetchProducts = async (page = 1) => {
    try {
        const params = {page}
        if (searchProductId.value.trim()) {
            params.product_id = searchProductId.value.trim()
        }
        const response = await axios.get('/api/products', {params})
        products.value = response.data
    } catch (error) {
        console.error('Error fetching products:', error)
    }
}

const handleFileUpload = (event) => {
    selectedFile.value = event.target.files[0] || null
}

const uploadFile = async () => {
    if (!selectedFile.value) return;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await axios.post(
            '/api/products/upload',
            formData,
            {
                headers: {'Content-Type': 'multipart/form-data'},
            }
        );

        const jobId = response.data.job_id;

        toast.success('File uploaded! Processing in queue...');
        selectedFile.value = null;

        const poller = setInterval(async () => {
            try {
                const statusResponse = await axios.get(`/api/queue/status/${jobId}`);

                if (statusResponse.data.completed) {
                    clearInterval(poller);
                    toast.success('Processing completed!');
                    await fetchProducts(1);
                }

                if (statusResponse.data.failed) {
                    clearInterval(poller);
                    toast.error('Processing failed!');
                }
            } catch (error) {
                clearInterval(poller);
                toast.error('Error checking status');
            }
        }, 2000);

    } catch (error) {
        toast.error('Upload failed');
        console.error('Upload error:', error);
    }
};

onMounted(() => {
    fetchProducts()
})
</script>

<style scoped>
.product-list {
    max-width: 900px;
    margin: 2rem auto;
    font-family: 'Poppins', sans-serif;
    color: #333;
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
}


h1 {
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #1e40af;
    text-align: center;
}

.search-upload {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    justify-content: center;
}

.search-upload input[type='text'] {
    flex-grow: 1;
    min-width: 220px;
    padding: 0.6rem 1rem;
    font-size: 1rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    transition: border-color 0.2s ease;
}

.search-upload input[type='text']:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgb(59 130 246 / 0.3);
}

.search-upload input[type='file'] {
    flex-basis: 180px;
}

.search-upload button {
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0.6rem 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
    min-width: 100px;
}

.search-upload button:disabled {
    background-color: #93c5fd;
    cursor: not-allowed;
}

.search-upload button:not(:disabled):hover {
    background-color: #2563eb;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

thead tr {
    background-color: #e0e7ff;
    color: #1e40af;
}

th, td {
    text-align: left;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #d1d5db;
}

tbody tr:hover {
    background-color: #f0f4ff;
    cursor: default;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    font-weight: 600;
    color: #1e40af;
}

.pagination button {
    background-color: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.pagination button:disabled {
    background-color: #93c5fd;
    cursor: not-allowed;
}

.pagination button:not(:disabled):hover {
    background-color: #2563eb;
}

.pagination button.active {
    background-color: white;
    color: #2563eb;
    border: 2px solid #2563eb;
}

/* Responsive tweaks */
@media (max-width: 600px) {
    .search-upload {
        flex-direction: column;
        align-items: stretch;
    }

    .search-upload input[type='file'], .search-upload button {
        flex-basis: auto;
        width: 100%;
    }

    th, td {
        padding: 0.5rem;
        font-size: 0.85rem;
    }
}
</style>

