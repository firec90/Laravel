Ceritanya kita ingin membuat Project CRUD satu tabel dengan ketentuan Laravel 12 sebagai backend, VueJS sebagai frontend, masing-masing backend dan frontend tersebut didalam Folder Project CRUD, jadi buat 2 folder didalamnya dinamai backend dan frontend
##### <div align="center">BACKEND</div>
1. Membuat backend Laravel 12
```
composer create-project laravel/laravel backend
cd backend
```

2. Jalankan server Laravel, untuk memastikan Laravel berjalan dengan baik
```
php artisan serve
```

3. Buka browser dan akses alamat berikut http://127.0.0.1:8000

4. Ubah sebagian file “.env” menjadi berikut
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_db
DB_USERNAME=root
DB_PASSWORD=
```

5. Pastikan koneksi ke MySQL aktif dengan mengetikkan perintah berikut ke terminal, nanti hasilnya menunjukkan ada kata MySQL :
```
php artisan tinker
>>> DB::connection()->getPdo();
```
	
6. Membuat MODEL + MIGRASI untuk product
```
php artisan make:model Product -m
```
	arti -m : migration
	Hasilnya:
	• File model: app/Models/Product.php
	• File migrasi: database/migrations/2025_10_17_XXXXXX_create_products_table.php

7. Edit migrasi tabel products
• Buka file database/migrations/2025_10_16_XXXXXX_create_products_table.php
• Ubah isi up() menjadi seperti ini:
```
public function up(): void
{
	Schema::create('product', function (Blueprint $table) {
		$table->id('product_id');
		$table->string('product_name', 200)->nullable();
		$table->double('product_price')->nullable();
		$table->timestamps();
	});
}
```
	
8. Jalankan migrasi
```
php artisan migrate
```
	Jika muncul:
	Migrating: 2025_10_16_XXXXXX_create_products_table
	Migrated:  2025_10_16_XXXXXX_create_products_table (xx ms)
	Berarti berhasil

9. Membuat CONTROLLER untuk API
```
php artisan make:controller ProductController –api
```
	Arti dari –api : membuat Controller khusus untuk api
	Lokasi file : app/Http/Controllers/ProductController.php
	
10.	Ubah isi dari app/Http/Controllers/ProductController.php menjadi seperti ini :
```
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	// GET /api/products
	public function index()
	{
		return response()->json(Product::all());
	}

	// POST /api/products
	public function store(Request $request)
	{
		$validated = $request->validate([
			'product_name' => 'required|string|max:200',
			'product_price' => 'required|numeric',
		]);

		$product = Product::create($validated);
		return response()->json($product, 201);
	}

	// GET /api/products/{id}
	public function show($id)
	{
		$product = Product::findOrFail($id);
		return response()->json($product);
	}

	// PUT /api/products/{id}
	public function update(Request $request, $id)
	{
		$product = Product::findOrFail($id);
		$product->update($request->all());
		return response()->json($product);
	}

	// DELETE /api/products/{id}
	public function destroy($id)
	{
		$product = Product::findOrFail($id);
		$product->delete();
		return response()->json(['message' => 'Product deleted successfully']);
	}
}
```
	
11.	Aktifkan Mass Assignment di Model
Buka file “app/Models/Product.php”
Tambahkan properti $fillable agar data dari request bisa disimpan ke database:
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasFactory;

	protected $table = 'product';
	protected $primaryKey = 'product_id';

	protected $fillable = [
		'product_name',
		'product_price',
	];
}
```

12.	Tambahkan Route API
File ada di “routes/api.php”, kalau tidak ada buat saja
Isinya adalah seperti berikut :
```
<?php

use App\Http\Controllers\ProductController;

Route::apiResource('products', ProductController::class);
```

13.	Daftarkan routes/api.php di Laravel 12
Laravel 12 tidak otomatis membaca “routes/api.php”, jadi kita perlu daftarkan manual.
Buka “bootstrap/app.php”
Cari bagian :
```
->withRouting(
	web: __DIR__.'/../routes/web.php',
	commands: __DIR__.'/../routes/console.php',
	health: '/up',
)
```

Ubah menjadi :
```
->withRouting(
	web: __DIR__.'/../routes/web.php',
	api: __DIR__.'/../routes/api.php',
	commands: __DIR__.'/../routes/console.php',
	health: '/up',
)
```

14.	Tes Route
Buka terminal dan jalankan perintah “php artisan route:list”
Jika semuanya benar, kamu akan melihat hasil seperti ini :
> GET|HEAD	api/products
> POST		api/products
> GET|HEAD	api/products/{product}
> PUT|PATCH	api/products/{product}
> DELETE		api/products/{product}

15.	Tes API di browser atau menggunakan CURL
	> SELECT (GET semua produk) :
	> curl -X GET http://127.0.0.1:8000/api/products

	> INSERT (POST – tambah produk baru) :
	> curl -X POST http://127.0.0.1:8000/api/products -H "Content-Type: application/json" -d '{"product_name": "Mouse Wireless", "product_price": 150000}'

	> SELECT by ID (GET satu produk) :
	> curl -X GET http://127.0.0.1:8000/api/products/2

	> UPDATE (PUT – ubah data produk) :
	> curl -X PUT http://127.0.0.1:8000/api/products/2 -H "Content-Type: application/json" -d '{"product_name": "Mouse Wireless", "product_price": 175000}'

	> DELETE (hapus produk)
	> curl -X DELETE http://127.0.0.1:8000/api/products/2

##### <div align="center">FRONTEND</div>
1. Membuat proyek VueJS
Masuk ke folder utama project (bukan folder backend), lalu jalankan :
```
npm create vue@latest frontend
```
	Saat muncul pertanyaan:
	• Project name: frontend
	• Add TypeScript? ❌ No
	• Add JSX Support? ❌ No
	• Add Router for Single Page Application? ✅ Yes
	• Add Pinia for state management? ❌ No (belum perlu)
	• Add Vitest for Unit Testing? ❌ No
	• Add ESLint for code quality? ✅ Yes
	• Add Prettier for code formatting? ✅ Yes

Setelah selesai jalankan perintah berikut :
```
cd frontend
npm install
npm run dev
```

Buka di browser URL yang muncul (biasanya `http://localhost:5173`) → pastikan tampilan Vue default muncul

2. Tambahkan Bootstrap 5
	Jalankan di terminal `npm install bootstrap`
	Lalu buka file `src/main.js`, ubah jadi seperti ini :
	```
	import { createApp } from 'vue'
	import App from './App.vue'
	import router from './router'

	import 'bootstrap/dist/css/bootstrap.min.css'
	import 'bootstrap/dist/js/bootstrap.bundle.min.js'

	createApp(App).use(router).mount('#app')
	```
	> [!NOTE] Ini akan mengaktifkan semua komponen dan gaya Bootstrap di seluruh app Vue.

3. Uji Bootstrap
	Edit file src/App.vue menjadi sederhana :
	```
	<template>
	<div class="container mt-5">
		<h1 class="text-center text-primary">Vue + Bootstrap + Laravel API</h1>
		<button class="btn btn-success mt-3">Tes Tombol Bootstrap</button>
	</div>
	</template>

	<script setup>
	</script>

	<style>
	body {
	background-color: #f8f9fa;
	}
	</style>
	```

	Kemudian jalankan lagi `npm run dev`
	Kalau tombol hijau muncul dengan gaya Bootstrap → artinya sukses
<br>
4. Koneksi API + Tampilkan Data Produk
	* Install Axios, jalankan di terminal (di dalam folder `frontend`) : `npm install axios`
	* Buat file service untuk koneksi API
		Biar rapi, kita buat folder `src/services` lalu file `api.js`
		Lokasi file `frontend/src/services/api.js`
		```
		import axios from 'axios'

		const api = axios.create({
			baseURL: 'http://127.0.0.1:8000/api', // alamat backend Laravel kamu
		})

		export default api
		```
	* Buat halaman untuk menampilkan produk
		Sekarang, ubah file src/views/HomeView.vue menjadi seperti ini :
		```
		<template>
			<div class="container mt-5">
				<h2 class="mb-4 text-center text-primary">Daftar Produk</h2>

				<div class="mb-3 text-end">
				<button class="btn btn-success" @click="getProducts">Muat Ulang Data</button>
				</div>

				<table class="table table-bordered table-striped">
				<thead class="table-dark">
					<tr>
					<th>ID</th>
					<th>Nama Produk</th>
					<th>Harga</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="p in products" :key="p.product_id">
					<td>{{ p.product_id }}</td>
					<td>{{ p.product_name }}</td>
					<td>Rp {{ p.product_price.toLocaleString() }}</td>
					</tr>
				</tbody>
				</table>
			</div>
		</template>

		<script setup>
		import { ref, onMounted } from 'vue'
		import api from '../services/api'

		const products = ref([])

		const getProducts = async () => {
		try {
			const res = await api.get('/products')
			products.value = res.data
		} catch (err) {
			console.error('Gagal memuat produk:', err)
		}
		}

		onMounted(getProducts)
		</script>
		```
		Penjelasan singkat :
		* ref([]) digunakan untuk menyimpan data produk.
		* onMounted(getProducts) akan otomatis memanggil API Laravel saat halaman pertama kali dibuka.
		* Hasilnya langsung ditampilkan di tabel Bootstrap
<br>
5. Setelah langkah ke 4 tadi (`Koneksi API + Tampilkan Data Produk`), anda tidak mengakses `src/views/HomeView.vue`, tapi ke `src/App.vue`. Ya itu betul sekali karena secara default, **Vue Router** mengatur tampilan awal aplikasi untuk menampilkan `App.vue`. Untuk case ini kita masukkan `HomeView.vue` ini ke dalam `App.vue` dengan `<router-view />`.
Jadi artinya:
	* File `App.vue` adalah layout utama.
	* File `HomeView.vue` adalah halaman (route) yang muncul di dalam layout itu (`App.vue`).
<br>
6. Agar berjalan dengan baik, maka ubah source code pada `src/App.vue` seperti berikut :
	```
	<template>
	<div>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">My POS</a>
		</div>
		</nav>

		<!-- Router outlet -->
		<router-view />
	</div>
	</template>

	<script setup>
	</script>
	```
	Perhatikan pada `<router-view />`, itu adalah tempat halaman (`HomeView.vue, dll`) akan dimasukkan secara otomatis oleh Vue Router.
<br>
7. Pastikan rute diarahkan ke `HomeView.vue`. Buka dan edit file `src/router/index.js` :
	```
	import { createRouter, createWebHistory } from 'vue-router'
	import HomeView from '../views/HomeView.vue'

	const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
		path: '/',
		name: 'home',
		component: HomeView,
		},
	],
	})

	export default router
	```
	Ini memastikan URL `/` memuat `HomeView.vue`, lalu jalankan lagi `npm run dev`.
	Lalu buka `http://localhost:5173` pada browser.
	Kalau sudah benar :
	* Navbar hitam muncul di atas (`template dari App.vue yang ada tulisan My POS`)
	* Dan tabel produk dari backend Laravel tampil di bawahnya (`template dari HomeView.vue yang ada daftar product`)

8. Tambah Produk (Create)
	Tambahkan form input di atas tabel dengan mengubah isi file `src/views/HomeView.vue` seperti ini :
	```
	<template>
	<div class="container mt-5">
		<h2 class="mb-4 text-center text-primary">Daftar Produk</h2>

		<!-- Form Tambah Produk -->
		<div class="card mb-4">
			<div class="card-header bg-success text-white">Tambah Produk Baru</div>
			<div class="card-body">
				<form @submit.prevent="addProduct">
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
					<button type="submit" class="btn btn-success">Tambah</button>
					</div>
				</div>
				</form>
			</div>
		</div>

		<!-- Tombol refresh -->
		<div class="mb-3 text-end">
		<button class="btn btn-outline-primary" @click="getProducts">Muat Ulang Data</button>
		</div>

		<!-- Tabel produk -->
		<table class="table table-bordered table-striped">
		<thead class="table-dark">
			<tr>
			<th>ID</th>
			<th>Nama Produk</th>
			<th>Harga</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="p in products" :key="p.product_id">
			<td>{{ p.product_id }}</td>
			<td>{{ p.product_name }}</td>
			<td>Rp {{ p.product_price.toLocaleString() }}</td>
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

	onMounted(getProducts)
	</script>
	```

9. Edit Produk (Update)
Tujuannya : kita akan menambahkan tombol **Edit** di tabel, lalu menampilkan data produk ke form, ubah nilainya, dan kirim ke Laravel pakai `PUT /api/products/:id`
	a). Ubah Template Tabel dengan menambahkan kolom **“Aksi**” dan tombol **Edit** di tabel kamu (`src/views/HomeView.vue`)
	```
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
		<td>
			<button class="btn btn-warning btn-sm me-2" @click="editProduct(p)">Edit</button>
		</td>
		</tr>
	</tbody>
	</table>
	```

	b). Tambahkan Mode Edit di Form Tambah
	Kita ubah form agar bisa berfungsi ganda : 
	Jika sedang menambah → tombolnya bertuliskan “Tambah”
	Jika sedang mengedit → tombolnya bertuliskan “Simpan Perubahan”
	Tambahkan beberapa variabel dan logika di bagian `<script setup>`:
	```
	const editing = ref(false)
	const editId = ref(null)

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
	```

	c). Ubah bagian `<form>` supaya dinamis
	Ubah form menjadi seperti ini:
	```
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
	</div>
	<div v-if="editing" class="text-end mt-2">
		<button type="button" class="btn btn-secondary btn-sm" @click="cancelEdit">Batal</button>
	</div>
	</form>
	```
	> Penjelasan :
	> * editing menandakan apakah user sedang mengedit produk.
	> * Kalau editing = true, form akan kirim data ke fungsi updateProduct() (PUT).
	> * Tombol “Batal” mengembalikan form ke mode Tambah.

10. Hapus Produk (Delete)
	Kita hanya akan menambahkan sedikit kode di file yang sama (`src/views/HomeView.vue`)
	a). Tambahkan tombol Hapus di tabel
	`src/views/HomeView.vue`
	```
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
		<td>
			<button class="btn btn-warning btn-sm me-2" @click="editProduct(p)">Edit</button>
			<button class="btn btn-danger btn-sm" @click="deleteProduct(p.product_id)">Hapus</button>
		</td>
		</tr>
	</tbody>
	</table>
	```

	b). Tambahkan fungsi untuk menghapus produk
	Tambahkan kode ini di bagian `<script setup>` kamu (di bawah `cancelEdit` misalnya):
	```
	const deleteProduct = async (id) => {
	if (!confirm('Yakin ingin menghapus produk ini?')) return

	try {
		await api.delete(`/products/${id}`)
		await getProducts()
	} catch (err) {
		console.error('Gagal menghapus produk:', err)
		}
	}
	```

	> Penjelasan singkat :
	> * Fungsi `confirm()` menampilkan dialog konfirmasi sebelum menghapus
	> * `api.delete()` memanggil endpoint Laravel `DELETE /api/products/:id`
	> * Setelah berhasil, data tabel diperbarui dengan `getProducts()`

#### <div align="center"> MENGGANTI Eloquent ORM menjadi query SQL asli (raw query), bukan Query Builder </div>
1. Buka file controller kamu :
`backend/app/Http/Controllers/ProductController.php`
Sekarang kita ganti semua fungsi CRUD-nya supaya memakai Query MySQL langsung, bukan Eloquent.
<br>
2. Import DB Facade, tambahkan kode ini paling atas : `use Illuminate\Support\Facades\DB;`
Gunakan Query MySQL di setiap fungsi
	a). GET (semua produk)
	```
	public function index()
	{
		// SELECT * FROM product ORDER BY product_id DESC
		$products = DB::select('SELECT * FROM product ORDER BY product_id DESC');
		return response()->json($products);
	}
	```

	b). POST (tambah produk)
	```
	public function store(Request $request)
	{
		DB::insert(
			'INSERT INTO product (product_name, product_price) VALUES (?, ?)',
			[$request->product_name, $request->product_price]
		);

		return response()->json(['message' => 'Produk berhasil ditambahkan']);
	}
	```

	c). GET by ID
	```
	public function show($id)
	{
		$product = DB::select('SELECT * FROM product WHERE product_id = ?', [$id]);
		if (!$product) {
			return response()->json(['message' => 'Produk tidak ditemukan'], 404);
		}
		return response()->json($product[0]);
	}
	```

	d). PUT (update produk)
	```
	public function update(Request $request, $id)
	{
		$affected = DB::update(
			'UPDATE product SET product_name = ?, product_price = ? WHERE product_id = ?',
			[$request->product_name, $request->product_price, $id]
		);

		if ($affected) {
			return response()->json(['message' => 'Produk berhasil diperbarui']);
		} else {
			return response()->json(['message' => 'Produk tidak ditemukan'], 404);
		}
	}
	```

	e). DELETE (hapus produk)
	```
	public function destroy($id)
	{
		$deleted = DB::delete('DELETE FROM product WHERE product_id = ?', [$id]);

		if ($deleted) {
			return response()->json(['message' => 'Produk berhasil dihapus']);
		} else {
			return response()->json(['message' => 'Produk tidak ditemukan'], 404);
		}
	}
	```

#### <div align="center"> API tidak boleh diakses secara langsung via Javascript dari Browser </div>
1. Kita aktifkan CORS whitelist, agar tidak bisa diakses langsung dari Browser, setelah Laravel 11 keatas `config/cors.php` sudah dihapus, kalau Laravel 12 berada pada `bootstrap/app.php`, buka file tersebut. Perlu diingat ya API ini tidak tidak bisa diakses via **Javascript yang berjalan di Browser**, kalau kita menuliskan path pada Browser, API tetap bisa diakses dan itu pencegahannya dengan motode lain lagi
<br>
2. Buat file baru `backend/config/cors.php` kalau tidak ditemukan file tersebut, isikan file tersebut dengan kode berikut :
	```
	<?php
	return [
		/*
		|--------------------------------------------------------------------------
		| Cross-Origin Resource Sharing (CORS) Configuration
		|--------------------------------------------------------------------------
		|
		| Kamu bisa menentukan domain mana saja yang diizinkan untuk mengakses API.
		| Ini penting agar backend Laravel hanya bisa diakses oleh frontend tertentu.
		|
		*/

		'paths' => ['api/*', 'sanctum/csrf-cookie'],
		'allowed_methods' => ['*'],
		// Ganti URL berikut dengan alamat frontend VueJS kamu
		'allowed_origins' => ['http://localhost:5173'],
		'allowed_origins_patterns' => [],
		'allowed_headers' => ['*'],
		'exposed_headers' => [],
		'max_age' => 0,
		'supports_credentials' => true,
	];
	```
<br>

3. Tambahkan middleware HandleCors di `backend/bootstrap/app.php`, pastikan ada baris seperti ini :
	```
	use Illuminate\Http\Middleware\HandleCors;

	return Application::configure(basePath: dirname(__DIR__))
		->withRouting(
			web: __DIR__.'/../routes/web.php',
			api: __DIR__.'/../routes/api.php',
			commands: __DIR__.'/../routes/console.php',
			health: '/up',
		)
		->withMiddleware(function (Middleware $middleware) {
			$middleware->append(HandleCors::class);
		})
		->create();
	```
<br>

4. Bersihkan konfigurasi & restart server

	```
	php artisan config:clear
	php artisan route:clear
	php artisan serve
	```

<br>

#### <div align="center">Melindungi API dengan Laravel Sanctum </div>
Dengan menggunakan Laravel Sanctum, API tidak bisa diakses tanpa melalui Login atau Register terlebih dahulu yang selanjutnya membuat token untuk dipakai request API
1. Instalasi & persiapan Sanctum (jika belum)
	Jalankan perintah ini di folder `backend`:
	```
	composer require laravel/sanctum
	php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
	php artisan migrate atau php artisan migrate:fresh
	```
	Penjelasan singkat:
	* composer require → memasang paket Sanctum.
	* vendor:publish → menyalin file konfigurasi (config/sanctum.php) & migration yang diperlukan.
	* migrate → membuat tabel personal_access_tokens yang dipakai Sanctum untuk menyimpan token.
	* fresh → kalau kamu pernah error table already exists
<br>
2. Ubah model User
	Buka file `app/Models/User.php`
	Tambahkan trait Sanctum:
	```
	use Laravel\Sanctum\HasApiTokens;

	class User extends Authenticatable
	{
	    use HasApiTokens, HasFactory, Notifiable;
	}
	```
	> Artinya: setiap user bisa memiliki token API sendiri, disimpan di tabel `personal_access_tokens`

3. Konfigurasi Sanctum & CORS di `bootstrap/app.php`. Laravel 12 tidak punya `Kernel.php` atau `config/cors.php`, jadi semua konfigurasi kita taruh di `bootstrap/app.php`. Buka `backend/bootstrap/app.php`, isi atau ubah jadi seperti ini :
	```
	<?php

	use Illuminate\Foundation\Application;
	use Illuminate\Foundation\Configuration\Middleware;
	use Illuminate\Http\Middleware\HandleCors;
	use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
	use Illuminate\Support\Facades\Config;

	Config::set('cors', [
		'paths' => ['api/*', 'sanctum/csrf-cookie'],
		'allowed_methods' => ['*'],
		'allowed_origins' => ['http://localhost:5173'], // alamat frontend VueJS
		'allowed_headers' => ['*'],
		'supports_credentials' => true,
	]);

	return Application::configure(basePath: dirname(__DIR__))
		->withRouting(
			web: __DIR__ . '/../routes/web.php',
			api: __DIR__ . '/../routes/api.php',
			commands: __DIR__ . '/../routes/console.php',
			health: '/up',
		)
		->withMiddleware(function (Middleware $middleware) {
			// aktifkan CORS
			$middleware->append(HandleCors::class);

			// aktifkan Sanctum stateful middleware untuk SPA (VueJS)
			$middleware->statefulApi()
				->prepend(EnsureFrontendRequestsAreStateful::class);
		})
		->create();
	```

	Penjelasan singkat:
	> * HandleCors → mengatur domain mana yang boleh akses API.
	> * EnsureFrontendRequestsAreStateful → memungkinkan VueJS login menggunakan cookie / token.
	> * statefulApi() → menandakan API tetap ingat session (bukan stateless).

4. Buat route login dan register, edit `routes/api.php` :
	```
	use App\Http\Controllers\AuthController;
	use App\Http\Controllers\ProductController;
	use Illuminate\Support\Facades\Route;

	// Route publik
	Route::post('/register', [AuthController::class, 'register']);
	Route::post('/login', [AuthController::class, 'login']);

	// Route yang butuh autentikasi
	Route::middleware('auth:sanctum')->group(function () {
		Route::apiResource('products', ProductController::class);
		Route::post('/logout', [AuthController::class, 'logout']);
	});
	```

CATATAN SAMPAI SINI