<template>
  <div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Daftar Produk</h2>

    <!-- Form Tambah Produk -->
	<div class="card mb-4">
    <div class="card-header bg-success text-white">Tambah Produk Baru</div>
    <div class="card-body">
      <form @submit.prevent="editing ? updateProduct() : addProduct()">
        <div class="row g-3">
          <div class="col-md-6">
          <input
            v-model="newProduct.product_name"
            type="text"
            class="form-control"
            placeholder="Nama Produk"
            required
          />
          </div>
          <div class="col-md-4">
          <input
            v-model.number="newProduct.product_price"
            type="number"
            class="form-control"
            placeholder="Harga Produk"
            required
          />
          </div>
          <div class="col-md-2 d-grid">
          <button type="submit" class="btn" :class="editing ? 'btn-warning' : 'btn-success'">
            {{ editing ? 'Simpan Perubahan' : 'Tambah' }}
          </button>
          </div>
          <div v-if="editing" class="text-end mt-2">
            <button type="button" class="btn btn-secondary btn-sm" @click="cancelEdit">Batal</button>
          </div>
        </div>
      </form>
    </div>
	</div>

    <!-- Tombol refresh -->
    <div class="mb-3 text-end">
      <button class="btn btn-success" @click="getProducts">Muat Ulang Data</button>
    </div>

    <!-- Tabel produk -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in products" :key="p.product_id">
          <td>{{ p.product_id }}</td>
          <td>{{ p.product_name }}</td>
          <td>Rp {{ p.product_price.toLocaleString() }}</td>
          <td><button class="btn btn-warning btn-sm me-2" @click="editProduct(p)">Edit</button>
          <button class="btn btn-danger btn-sm" @click="deleteProduct(p.product_id)">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const products = ref([])
const newProduct = ref({
  product_name: '',
  product_price: null,
})
const editing = ref(false)
const editId = ref(null)

const getProducts = async () => {
  try {
    const res = await api.get('/products')
    products.value = res.data
  } catch (err) {
    console.error('Gagal memuat produk:', err)
  }
}

const addProduct = async () => {
  try {
    await api.post('/products', newProduct.value)
    newProduct.value = { product_name: '', product_price: null }
    await getProducts()
  } catch (err) {
    console.error('Gagal menambah produk:', err)
  }
}

const editProduct = (product) => {
  newProduct.value = {
    product_name: product.product_name,
    product_price: product.product_price,
  }
  editId.value = product.product_id
  editing.value = true
  window.scrollTo({ top: 0, behavior: 'smooth' }) // scroll ke form
}

const updateProduct = async () => {
  try {
    await api.put(`/products/${editId.value}`, newProduct.value)
    editing.value = false
    editId.value = null
    newProduct.value = { product_name: '', product_price: null }
    await getProducts()
  } catch (err) {
    console.error('Gagal mengubah produk:', err)
  }
}

const cancelEdit = () => {
  editing.value = false
  editId.value = null
  newProduct.value = { product_name: '', product_price: null }
}

const deleteProduct = async (id) => {
if (!confirm('Yakin ingin menghapus produk ini?')) return

try {
	await api.delete(`/products/${id}`)
	await getProducts()
} catch (err) {
	console.error('Gagal menghapus produk:', err)
	}
}

onMounted(getProducts)
</script>
