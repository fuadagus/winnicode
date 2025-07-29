<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NewsSeeder extends Seeder
{
    /**
     * Download image from URL and save to storage, or use existing local images
     */
    private function downloadImage($url, $filename)
    {
        try {
            // Tambahkan user agent dan headers untuk menghindari blocking
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->timeout(15)->get($url);
            
            if ($response->successful()) {
                $imageContent = $response->body();
                
                // Pastikan direktori images ada
                if (!Storage::disk('public')->exists('images')) {
                    Storage::disk('public')->makeDirectory('images');
                }
                
                // Simpan gambar
                Storage::disk('public')->put('images/' . $filename, $imageContent);
                
                return $filename;
            }
        } catch (\Exception $e) {
            // Jika gagal download, gunakan gambar yang sudah ada
            $existingImages = [
                'pict1.jpeg', 'pict2.jpeg', 'pict3.jpeg', 'pict4.jpeg', 'pict5.jpeg', 'pict6.jpeg'
            ];
            
            $randomImage = $existingImages[array_rand($existingImages)];
            $sourcePath = public_path('img/' . $randomImage);
            
            if (file_exists($sourcePath)) {
                // Pastikan direktori images ada
                if (!Storage::disk('public')->exists('images')) {
                    Storage::disk('public')->makeDirectory('images');
                }
                
                // Copy gambar yang sudah ada
                $targetPath = storage_path('app/public/images/' . $filename);
                copy($sourcePath, $targetPath);
                
                return $filename;
            }
        }
        
        return null;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada kategori terlebih dahulu
        $categories = [
            ['name' => 'Teknologi', 'views' => 0],
            ['name' => 'Olahraga', 'views' => 0],
            ['name' => 'Politik', 'views' => 0],
            ['name' => 'Ekonomi', 'views' => 0],
            ['name' => 'Kesehatan', 'views' => 0],
            ['name' => 'Pendidikan', 'views' => 0],
            ['name' => 'Hukum', 'views' => 0],
            ['name' => 'Bencana', 'views' => 0],
            ['name' => 'Gaya Hidup', 'views' => 0],
            ['name' => 'Hiburan', 'views' => 0],
            ['name' => 'Otomotif', 'views' => 0],
            ['name' => 'Nasional', 'views' => 0],
            ['name' => 'Internasional', 'views' => 0],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Ambil user dengan role Writer untuk dijadikan author
        $writers = User::role('Writer')->get();
        if ($writers->isEmpty()) {
            // Fallback ke semua user jika tidak ada Writer
            $writers = User::all();
        }
        $categoryIds = Category::pluck('id')->toArray();

        if ($writers->isEmpty() || empty($categoryIds)) {
            $this->command->error('Tidak ada writer atau kategori yang tersedia. Jalankan seeder User dan Category terlebih dahulu.');
            return;
        }

        // Data lokasi Indonesia yang lebih lengkap untuk berita dengan koordinat yang akurat
        $indonesianLocations = [
            ['city' => 'Jakarta Pusat', 'province' => 'DKI Jakarta', 'latitude' => -6.1745, 'longitude' => 106.8227],
            ['city' => 'Surabaya', 'province' => 'Jawa Timur', 'latitude' => -7.2575, 'longitude' => 112.7521],
            ['city' => 'Bandung', 'province' => 'Jawa Barat', 'latitude' => -6.9175, 'longitude' => 107.6191],
            ['city' => 'Medan', 'province' => 'Sumatera Utara', 'latitude' => 3.5952, 'longitude' => 98.6722],
            ['city' => 'Semarang', 'province' => 'Jawa Tengah', 'latitude' => -6.9667, 'longitude' => 110.4167],
            ['city' => 'Makassar', 'province' => 'Sulawesi Selatan', 'latitude' => -5.1477, 'longitude' => 119.4327],
            ['city' => 'Palembang', 'province' => 'Sumatera Selatan', 'latitude' => -2.9761, 'longitude' => 104.7754],
            ['city' => 'Yogyakarta', 'province' => 'D.I. Yogyakarta', 'latitude' => -7.7956, 'longitude' => 110.3695],
            ['city' => 'Denpasar', 'province' => 'Bali', 'latitude' => -8.6500, 'longitude' => 115.2167],
            ['city' => 'Balikpapan', 'province' => 'Kalimantan Timur', 'latitude' => -1.2379, 'longitude' => 116.8529],
            ['city' => 'Manado', 'province' => 'Sulawesi Utara', 'latitude' => 1.4748, 'longitude' => 124.8421],
            ['city' => 'Padang', 'province' => 'Sumatera Barat', 'latitude' => -0.9471, 'longitude' => 100.4172],
            ['city' => 'Malang', 'province' => 'Jawa Timur', 'latitude' => -7.9797, 'longitude' => 112.6304],
            ['city' => 'Samarinda', 'province' => 'Kalimantan Timur', 'latitude' => -0.5017, 'longitude' => 117.1536],
            ['city' => 'Banjarmasin', 'province' => 'Kalimantan Selatan', 'latitude' => -3.3194, 'longitude' => 114.5906],
            ['city' => 'Pontianak', 'province' => 'Kalimantan Barat', 'latitude' => -0.0263, 'longitude' => 109.3425],
            ['city' => 'Jambi', 'province' => 'Jambi', 'latitude' => -1.6101, 'longitude' => 103.6131],
            ['city' => 'Pekanbaru', 'province' => 'Riau', 'latitude' => 0.5071, 'longitude' => 101.4478],
            ['city' => 'Bandar Lampung', 'province' => 'Lampung', 'latitude' => -5.4292, 'longitude' => 105.2610],
            ['city' => 'Mataram', 'province' => 'Nusa Tenggara Barat', 'latitude' => - 8.5833, 'longitude' => 116.1167],
            ['city' => 'Kupang', 'province' => 'Nusa Tenggara Timur', 'latitude' => -10.1667, 'longitude' => 123.5833],
            ['city' => 'Solo', 'province' => 'Jawa Tengah', 'latitude' => -7.5667, 'longitude' => 110.8167],
            ['city' => 'Bekasi', 'province' => 'Jawa Barat', 'latitude' => -6.2349, 'longitude' => 106.9896],
            ['city' => 'Bogor', 'province' => 'Jawa Barat', 'latitude' => -6.5971, 'longitude' => 106.8060],
        ];

        // Data berita berdasarkan data yang disediakan
       

$newsData = [
    [
        'title' => 'Begini Peran Anak Buah Wahyu Kenzo dalam Kasus Investasi Bodong',
        'content' => 'MALANG - Polisi memeriksa koordinator pengumpul dana dari anggota robot trading ATG Wahyu Kenzo. Kasus ini terus dikembangkan untuk mengungkap semua pihak yang terlibat.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/14/519/2780877/begini-peran-anak-buah-wahyu-kenzo-dalam-kasus-investasi-bodong-nlCzy5gTaS.jpg?w=300',
        'category_keywords' => ['Hukum'],
        'location' => ['city' => 'Kota Malang', 'province' => 'Jawa Timur', 'latitude' => -7.9797, 'longitude' => 112.6304]
    ],
    [
        'title' => 'Pengadilan Pakistan Minta Polisi Tunda Penangkapan Eks PM Imran Khan',
        'content' => 'Pengadilan tinggi Pakistan meminta polisi menangguhkan upaya penangkapan eks Perdana Menteri Imran Khan untuk meredakan tensi politik yang memanas di negara tersebut.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gfxweexxazrtnx3wqtejhr0k.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Lahore', 'province' => 'Punjab', 'latitude' => 31.5204, 'longitude' => 74.3587]
    ],
    [
        'title' => 'Sleman Mulai Siapkan Skenario Evakuasi Jika Eskalasi Erupsi Gunung Merapi Terus Meningkat',
        'content' => 'TEMPO.CO, Jakarta - BPPTKG Yogyakarta mencatat peningkatan aktivitas Gunung Merapi. Pemerintah Kabupaten Sleman mulai menyiapkan skenario evakuasi warga di lereng Merapi.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://i.ytimg.com/vi/r9yWm3xoJkA/maxresdefault.jpg',
        'category_keywords' => ['Bencana'],
        'location' => ['city' => 'Kabupaten Sleman', 'province' => 'Daerah Istimewa Yogyakarta', 'latitude' => -7.7828, 'longitude' => 110.3672]
    ],
    [
        'title' => 'Miras Milik Purnawirawan Polri Akan Dilimpahkan ke Polresta Tidore',
        'content' => 'Polsek Kecamatan Oba Utara akan melimpahkan barang bukti berupa minuman keras milik seorang Purnawirawan Polri ke Polresta Tidore untuk proses hukum lebih lanjut.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvqxrcs4wh5qkw0dxzeqb4r7.jpg',
        'category_keywords' => ['Hukum'],
        'location' => ['city' => 'Kota Tidore Kepulauan', 'province' => 'Maluku Utara', 'latitude' => 0.6585, 'longitude' => 127.4475]
    ],
    // [
    //     'title' => 'Serapan Beras Bulog Rendah, Pengamat: Pemerintah Bisa Wajibkan Penggilingan Setor ke Bulog',
    //     'content' => 'TEMPO.CO, Jakarta - Kemampuan Bulog menyerap gabah petani pada awal panen raya masih rendah. Pengamat menyarankan pemerintah membuat aturan wajib setor bagi penggilingan.',
    //     'status' => 'Pending',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/02/03/id_1178406/1178406_720.jpg',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => '4 Bandara di Yogyakarta dan Semarang Tak Terdampak Erupsi Merapi',
        'content' => 'Sekretaris Perusahaan AirNav Indonesia Rosedi mengatakan operasional penerbangan di bandara Yogyakarta hingga Semarang dalam keadaan aman terkendali pasca erupsi Gunung Merapi.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://bisnisnews.id/core/images/uploads/medium/gambar-20200327_ot58xu.jpg?w=648&h=340',
        'category_keywords' => ['Bencana'],
        'location' => ['city' => 'Kabupaten Sleman', 'province' => 'Daerah Istimewa Yogyakarta', 'latitude' => -7.7828, 'longitude' => 110.3672]
    ],
    // [
    //     'title' => 'Sri Mulyani Segera Lancarkan Reformasi Jilid II Kemenkeu, Siapa yang Disasar?',
    //     'content' => 'TEMPO.CO, Jakarta - Menteri Keuangan Sri Mulyani Indrawati menyatakan sedang merencanakan reformasi jilid II di Kementerian Keuangan, khususnya di Ditjen Pajak dan Bea Cukai.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/02/25/id_1184113/1184113_720.jpg',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Bocah di Jambi Diancam & Dipaksa Oral Seks: Kini Trauma Tak Mau Sekolah',
        'content' => 'Bocah laki-laki berusia 8 tahun di Kota Jambi menjadi korban pencabulan oleh tetangganya. Akibatnya, korban mengalami trauma berat dan tidak mau bersekolah.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1543320230/ua0ttd6awm5i6cdi79ov.jpg',
        'category_keywords' => ['Hukum'],
        'location' => ['city' => 'Kota Jambi', 'province' => 'Jambi', 'latitude' => -1.6101, 'longitude' => 103.6131]
    ],
    [
        'title' => 'Gegara Pimpinan DPR Tak Tanda Tangan, Mahfud MD dan PPATK Gagal Rapat Bareng Komisi III Bahas Transaksi Mencurigakan',
        'content' => 'Suara.com - Komisi III DPR RI batal menggelar rapat dengan Menko Polhukam Mahfud MD dan Kepala PPATK karena surat dari pimpinan DPR belum ditandatangani.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://media.suara.com/pictures/970x544/2022/09/06/57526-gedung-dpr-mpr-ri-ilustrasi-gedung-dpr-mpr-dpr-mpr.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Pertumbuhan Ekonomi Membaik Terdongkrak Konsumsi, Sri Mulyani Sebut Jokowi Akan Umumkan THR PNS',
    //     'content' => 'TEMPO.CO, Jakarta - Menteri Keuangan Sri Mulyani Indrawati yakin pertumbuhan ekonomi pada tahun ini membaik sejalan dengan prediksi lonjakan konsumsi saat Lebaran.',
    //     'status' => 'Pending',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2022/05/31/id_1114036/1114036_720.jpg',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => '6 Perusahaan yang Ternyata Milik Indra Priawan Suami Nikita Willy',
        'content' => 'JAKARTA- 6 perusahaan yang ternyata milik Indra Priawan suami Nikita Willy menarik untuk diulas. Ia adalah generasi ketiga yang mengelola PT Blue Bird.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/14/455/2780959/6-perusahaan-yang-ternyata-milik-indra-priawan-suami-nikita-willy-Olk4Q7DdJX.jpg?w=300',
        'category_keywords' => ['Gaya Hidup'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'PPATK Klarifikasi soal Rp300 T di Kemenkeu: Bukan Korupsi Pegawai',
    //     'content' => 'Kepala Pusat Pelaporan dan Analisis Transaksi Keuangan (PPATK) Ivan Yustiavandana mengklarifikasi dugaan adanya transaksi janggal Rp300 triliun tersebut bukanlah korupsi pegawai.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2022/07/06/keterangan-pers-terkait-aliran-dana-terlarang_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => '4 Kota Terbahagia di Sulawesi Utara',
        'content' => 'JAKARTA - 4 kota terbahagia di Sulawesi Utara. Provinsi ini secara keseluruhan memiliki 15 wilayah administrasi, yakni 4 kota dan 11 kabupaten, dengan IPM di atas 70%.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/17/320/2783127/4-kota-terbahagia-di-sulawesi-utara-fVq3WH3CEv.jpg?w=300',
        'category_keywords' => ['Gaya Hidup'],
        'location' => ['city' => 'Kota Manado', 'province' => 'Sulawesi Utara', 'latitude' => 1.4748, 'longitude' => 124.8421]
    ],
    // [
    //     'title' => 'Harga Batu Bara Terjun Bebas, Bayan Resources Ungkap Strategi',
    //     'content' => 'Jakarta, CNBC Indonesia - Direktur PT Bayan Resources Tbk (BYAN) Alexander Ery Wibowo menyebut agenda hilirisasi akan mendorong permintaan komoditas batu bara di dalam negeri.',
    //     'status' => 'Draft',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/15/optimisme-bisnis-batu-bara-kala-windfall-komoditas-berakhir-cnbc-indonesia-tv_169.png?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Subaru WRX Wagon Jadi Rebutan, Inden hingga Akhir 2023',
        'content' => 'Kehadiran Subaru WRX di Indonesia mendapat sambutan positif dari para penggemar dan loyalisnya. Hal ini membuat masa tunggu inden hingga akhir tahun.',
        'status' => 'Pending',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gsjx7bvc6nwpwxep3w1wzjsa.jpg',
        'category_keywords' => ['Otomotif'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Jurus Sri Mulyani Kumpulkan Influencer untuk Tangkis Kasus Kemenkeu, DPR Heran',
        'content' => 'Suara.com - Menteri Keuangan (Menkeu) Sri Mulyani Indrawati dinilai memilih jurus yang tak biasa untuk menangkis kasus di Kemenkeu dengan mengundang beberapa influencer.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://media.suara.com/pictures/970x544/2023/03/21/94826-menteri-keuangan-menkeu-sri-mulyani-indrawati-antara.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'MinyaKita Langka di Pasaran, Zulhas Klaim Terlalu Sukses dan Banyak yang Cari',
        'content' => 'Menteri Perdagangan (Mendag) Zulkifli Hasan (Zulhas) mengeklaim MinyaKita sangat diminati di pasaran. Menurut dia, program pemerintah tersebut terlalu sukses.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01ga64pn6sv9xytdrxy7mnd4kf.jpg',
        'category_keywords' => ['Ekonomi'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Hary Tanoesoedibjo Tekankan Pentingnya Adaptasi di Tengah Perkembangan Teknologi',
        'content' => 'JAKARTA Executive Chairman MNC Group Hary Tanoesoedibjo menekankan pentingnya beradaptasi karena semakin pesatnya perkembangan teknologi. Hal itu termasuk cara kita bekerja.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/16/320/2782259/hary-tanoesoedibjo-tekankan-pentingnya-adaptasi-di-tengah-perkembangan-teknologi-1QtZY8O3tR.jfif?w=300',
        'category_keywords' => ['Teknologi'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Histeris Saat Tahu Nani Wijaya Meninggal, Connie Sutedja: Ikhlas, tapi Sedih',
        'content' => 'Pesinetron senior Nani Wijaya meninggal dunia hari ini, Kamis (16/3). Jenazahnya disemayamkan di kediamannya di kawasan Sentul, Kab. Bogor.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvm5drhyyp3t452m3z018nbk.jpg',
        'category_keywords' => ['Hiburan'],
        'location' => ['city' => 'Kabupaten Bogor', 'province' => 'Jawa Barat', 'latitude' => -6.5951, 'longitude' => 106.8062]
    ],
    // [
    //     'title' => 'Frasa Gangguan Lainnya di UU Pemilu Digugat ke Mahkamah Konstitusi',
    //     'content' => 'TEMPO.CO, Jakarta - Advokat Viktor Santoso Tandiasa resmi mendaftarkan gugatan terhadap frasa ‘gangguan lainnya’ yang terdapat di UU Pemilu ke Mahkamah Konstitusi.',
    //     'status' => 'Pending',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2019/05/12/id_841166/841166_720.jpg',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Sah! Ini Jajaran Direksi dan Komisaris Baru Bank Mandiri',
    //     'content' => 'Jakarta, CNBCIndonesia - Rapat Umum Pemegang Saham (RUPS) Tahunan PT Bank Mandiri Tbk (BMRI) memutuskan untuk mengubah susunan direksi dan komisaris perseroan.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2018/01/11/1ba89161-627b-4042-8e82-4a3318ba1686_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Sri Mulyani Happy! 7,1 Juta Wajib Pajak Sudah Lapor SPT',
    //     'content' => 'Jakarta, CNBC Indonesia - Menteri Keuangan Sri Mulyani Indrawati mengungkapkan, realisasi wajib pajak yang sudah melaporkan Surat Pemberitahuan Tahunan (SPT) pajak telah mencapai 7,1 pelapor.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/02/28/menteri-keuangan-sri-mulyani-indrawati-dalam-economic-outlook-2023-dengan-tema-menjaga-momentum-ekonomi-di-tengah-ketidakpasti-24_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Beda Klaim Jokowi vs Polri Soal Impor Perlengkapan Polisi, Siapa yang Benar?',
        'content' => 'Suara.com - Presiden Joko Widodo alias Jokowi sempat menuangkan kekecewaannya lantaran segelintir instansi negara masih hobi mengimpor perlengkapan. Polri menjadi salah satu yang disinggung.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://media.suara.com/pictures/970x544/2023/03/16/23985-jokowi-saat-marah.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Tompi Dedikasikan Lagu Religi Terbarunya untuk Mendiang Ibu',
        'content' => 'Setelah 13 tahun berselang, penyanyi Tompi kembali merilis lagu religi. Kali ini, Tompi merilis lagu berjudul Ada Anak Bertanya Pada Bapaknya karya Sam Bimbo dan Taufiq Ismail.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvzcygyp0ask622vdhesymch.jpg',
        'category_keywords' => ['Hiburan'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Bank Indonesia Catat Kredit Perbankan Tumbuh Jadi 10,64 Persen di Februari 2023',
        'content' => 'Bank Indonesia atau BI mencatat kredit perbankan hingga Februari 2023 naik menjadi 10,64 persen. Angka tersebut meningkat apabila dibandingkan Januari 2023 sebesar 10,53 persen.',
        'status' => 'Pending',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1589880120/niu5sfxkdmf5jtiax9an.jpg',
        'category_keywords' => ['Ekonomi'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Bus ALS Angkut 16 Penumpang Terbakar di Jalan Lintas Bukittinggi-Medan',
    //     'content' => 'Satu unit bus penumpang terbakar di jalan lintas Bukittinggi-Medan. Seluruh penumpang dilaporkan selamat. Diduga karena arus pendek kelistrikan mobil.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2021/11/26/bus-vaksin-terbakar-di-muara-enim-1_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Bencana'],
    //     'location' => ['city' => 'Kota Bukittinggi', 'province' => 'Sumatera Barat', 'latitude' => -0.3041, 'longitude' => 100.3706]
    // ],
    [
        'title' => 'Harga BBM Non Subsidi Pertamina Naik Lagi Per Hari Ini, Ini Daftarnya',
        'content' => 'Suara.com - PT Pertamina (Persero) kembali menaikkan harga dua jenis BBM non-subsidinya. Dua jenis BBM tersebut adalah Pertamax dan Pertamax Turbo.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://media.suara.com/pictures/970x544/2023/01/03/73935-harga-bbm-pertamina-pertamax-pertalite.jpg',
        'category_keywords' => ['Ekonomi'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Soal Transaksi Rp 300 T di Kemenkeu, Mahfud: Perkembangannya Positif',
    //     'content' => 'Menteri Koordinator Bidang Politik, Hukum, dan Keamanan (Menko Polhukam) Mahfud MD menyatakan dirinya akan kembali menemui Menteri Keuangan Sri Mulyani Indrawati.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://cdn-asset.jawapos.com/wp-content/uploads/2023/03/Menkopolhukam-Mahfud-MD-Dan-Wamenkeu-Suahasil-Nazara-Dery-Ridwansah-2-640x480.jpg',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Guru Cirebon Dipecat Usai Komentar di IG RK Tak Niat Ajukan Gugatan',
    //     'content' => 'Seorang guru di SMK Telkom Sekar Kemuning Kota Cirebon, Jawa Barat, Muhamad Sabil Fadilah yang diberhentikan instansi usai meninggalkan komentar kritikan di Instagram Gubernur Jabar.',
    //     'status' => 'Draft',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/15/muhammad-sabil_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Kota Cirebon', 'province' => 'Jawa Barat', 'latitude' => -6.7169, 'longitude' => 108.5588]
    // ],
    // [
    //     'title' => 'Gunung Merapi Jateng Masih Siaga! Begini Kondisi Terbarunya..',
    //     'content' => 'Jakarta, CNBC Indonesia - Gunung Merapi yang berlokasi di Daerah Istimewa Yogyakarta (DIY), Jawa Tengah masih berstatus level III atau siaga.',
    //     'status' => 'Pending',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/12/1248048203_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Bencana'],
    //     'location' => ['city' => 'Kabupaten Sleman', 'province' => 'Daerah Istimewa Yogyakarta', 'latitude' => -7.7828, 'longitude' => 110.3672]
    // ],
    // [
    //     'title' => 'Aspri Wamenkumham Bakal Laporkan Balik Ketua IPW ke Bareskrim',
    //     'content' => 'Asisten Pribadi (Aspri) Wakil Menteri Hukum dan Hak Asasi Manusia (Wamenkumham) Edward Omar Sharif Hiariej, Yogi Rukmana, bakal melaporkan balik Ketua Indonesia Police Watch (IPW).',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2022/08/25/sugeng-teguh-santoso-1_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Hukum'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Harga Emas Amblas ke Rp1,054 Juta per Gram',
    //     'content' => 'Harga jual emas PT Aneka Tambang (Persero) Tbk atau Antam berada di posisi Rp1,054 juta per gram. Harga emas Antam per gram ini turun Rp10 ribu dibandingkan harga sebelumnya.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2018/11/12/e2edc0e5-f878-4478-946b-9b22048aaccf_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Tidak disebutkan', 'province' => 'Tidak disebutkan', 'latitude' => null, 'longitude' => null]
    // ],
    [
        'title' => 'Demo Anarkistis Tolak Reformasi Pensiun di Prancis: Balai Kota Bordeaux Dibakar',
        'content' => 'Aksi mogok kerja massal dan menentang reformasi usia pensiun di Prancis berubah menjadi anarkistis. Pintu depan Balai Kota Bordeaux dibakar pada Kamis malam.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gw62ch9yt4q85x0ntc0pj07v.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Bordeaux', 'province' => 'Nouvelle-Aquitaine', 'latitude' => 44.8378, 'longitude' => -0.5792]
    ],
    // [
    //     'title' => 'Laporan Transaksi Janggal Rafael Alun Mangkrak Bertahun-tahun di KPK, Ini Alasannya',
    //     'content' => 'TEMPO.CO, Jakarta - PPATK menyatakan telah mengirimkan laporan transaksi janggal pejabat Ditjen Pajak, Rafael Alun Trisambodo, ke penegak hukum sejak 2012.',
    //     'status' => 'Draft',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/03/02/id_1185367/1185367_720.jpg',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Menkeu AS dan Ketua The Fed Buka Suara Terkait Nasib SVB',
    //     'content' => 'Jakarta, CNBC Indonesia - Regulator keuangan dan perbankan AS meluncurkan langkah-langkah darurat untuk membendung potensi limpahan dari keruntuhan Silicon Valley Bank (SVB).',
    //     'status' => 'Pending',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2019/07/23/40230572-4ed1-41f0-aaa8-b09a66c9af3f_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Mikel Arteta Raih Manager of the Year di London Football Awards',
        'content' => 'Manajer Arsenal, Mikel Arteta, resmi menerima penghargaan sebagai Manager of the Year dalam ajang London Football Awards, Senin (13/3) kemarin.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01grhacpsn773t0xqtyekkahmy.jpg',
        'category_keywords' => ['Olahraga'],
        'location' => ['city' => 'London', 'province' => 'England', 'latitude' => 51.5072, 'longitude' => -0.1276]
    ],
    // [
    //     'title' => 'FOTO: PA 212 Demo Tolak Kedatangan Timnas Israel',
    //     'content' => 'Massa dari Persaudaraan Alumni (PA) 212 melakukan aksi unjuk rasa menolak kedatangan Timnas Israel untuk Piala Dunia U-20 di Patung Kuda, Jakarta.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/20/demo-tolak-kedatangan-timnas-israel-4_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Tidak disebutkan', 'province' => 'Tidak disebutkan', 'latitude' => null, 'longitude' => null]
    // ],
    [
        'title' => 'Ketua MPR: Tak Ada Tawar-menawar, LGBT Harus Dilawan',
        'content' => 'Ketua MPR RI Bambang Soesatyo alias Bamsoet mengajak masyarakat adat untuk menjaga identitas budaya serta ciri khas dan jati diri bangsa, termasuk mewaspadai LGBT.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gf067dh1rfybv2sfkdmpadmm.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Video: Tren Busana Lebaran, Motif Bunga Kecil Jadi Pilihan',
    //     'content' => 'Jakarta, CNBC Indonesia - Jelang bulan puasa, banyak yang mulai mencari baju khas lebaran. Tren busana tahun ini diprediksi akan didominasi motif bunga kecil.',
    //     'status' => 'Draft',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/20/cnbc-indonesia-tv-7_169.png?w=360&q=90',
    //     'category_keywords' => ['Gaya Hidup'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Pengertian dari Sudut Deklinasi dalam Pembelajaran Matematika',
        'content' => 'Sudut deklinasi adalah sudut antara utara sebenarnya dan jarum kompas atau utara magnet. Penyimpangan arah ini disebut dengan deklinasi.',
        'status' => 'Pending',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvce58pq9mwg2n5svxewwvqr.jpg',
        'category_keywords' => ['Pendidikan'],
        'location' => ['city' => 'Tidak disebutkan', 'province' => 'Tidak disebutkan', 'latitude' => null, 'longitude' => null]
    ],
    [
        'title' => 'Mahfud Respons Anies soal Ada Menko Mau Ubah Konstitusi: Tak Tahu, Tak Ada Itu',
        'content' => 'Menko Polhukam Mahfud MD angkat bicara soal pernyataan bacapres NasDem Anies Baswedan yang menyebut ada Menko di pemerintahan Jokowi berupaya mengubah konstitusi.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gv9tgcbn11cz5kehw221cfd9.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Hilirisasi Batu Bara Jokowi Terancam Gagal, Ini Pemicunya',
    //     'content' => 'Jakarta, CNBC Indonesia - Asosiasi Pertambangan Indonesia (IMA) menilai proyek gasifikasi batu bara menjadi dimethyl ether (DME) kemungkinan cukup sulit dikembangkan di Indonesia.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2021/11/22/aktivitas-bongkar-muat-batu-bara-10_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Kasus Mayat Mutilasi dalam Koper, Polisi Ungkap Perkiraan Waktu Kematian Korban',
        'content' => 'BOGOR - Polisi terus menyelidiki kasus mayat pria dimutilasi yang ditemukan dalam koper di wilayah Tenjo, Kabupaten Bogor, Jawa Barat. Korban diperkirakan meninggal kurang dari 12 jam.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/16/338/2782266/kasus-mayat-mutilasi-dalam-koper-polisi-ungkap-perkiraan-waktu-kematian-korban-TCFRRT83UC.jpg?w=300',
        'category_keywords' => ['Hukum'],
        'location' => ['city' => 'Kabupaten Bogor', 'province' => 'Jawa Barat', 'latitude' => -6.5951, 'longitude' => 106.8062]
    ],
    // [
    //     'title' => '20 Negara Mau Jadi \'Sekutu\' Rusia, RI Salah Satunya',
    //     'content' => 'Jakarta, CNBC Indonesia - Sejumlah negara dunia saat ini dilaporkan sedang berniat untuk menjadi mitra perdagangan Rusia dalam BRICS dan juga Organisasi Kerja Sama Shanghai (SCO).',
    //     'status' => 'Draft',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2022/09/15/1182276215_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => '4 Orang dalam 1 Keluarga Tewas Gara-Gara Kepercayaan pada Teori Konspirasi',
        'content' => 'JENEWA - Penyelidik Swiss menyimpulkan bahwa kematian empat orang dalam satu keluarga yang terjadi tahun lalu disebabkan oleh teori konspirasi dan bunuh diri kolektif.',
        'status' => 'Pending',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/22/18/2785608/4-orang-dalam-1-keluarga-tewas-gara-gara-kepercayaan-pada-teori-konspirasi-FannNVl1OQ.jpg?w=300',
        'category_keywords' => ['Internasional'],
        'location' => ['city' => 'Montreux', 'province' => 'Vaud', 'latitude' => 46.4312, 'longitude' => 6.9114]
    ],
    [
        'title' => 'Bos Pertamina Ungkap Awal Mula Warga Padati Lahan Depo Plumpang',
        'content' => 'Direktur Utama PT Pertamina (Persero), Nicke Widyawati, menjelaskan awal mula kenapa Depo Plumpang terletak di tengah kota dan bisa sangat berdekatan dengan pemukiman warga.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gtpf5erzzf85gr66hw7jtq2c.jpg',
        'category_keywords' => ['Ekonomi'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Pemuda Diminta Tampil Konstruktif, Artikulatif, dan Visioner Songsong Indonesia Emas',
    //     'content' => 'INFO NASIONAL - Menyongsong Indonesia Emas 2045, Gerakan Pemuda Al Washliyah diminta tampil dengan konstruktif, artikulatif, dan visioner, serta bisa berkolaborasi.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/02/23/id_1183562/1183562_720.jpg',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Tidak disebutkan', 'province' => 'Tidak disebutkan', 'latitude' => null, 'longitude' => null]
    // ],
    // [
    //     'title' => 'LPSK Cabut Perlindungan karena Richard Eliezer Langgar Kesepakatan',
    //     'content' => 'TEMPO.CO, Jakarta - Lembaga Perlindungan Saksi dan Korban (LPSK) mengungkap alasan mencabut perlindungan fisik terhadap Richard Eliezer karena melanggar kesepakatan.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/02/27/id_1184626/1184626_720.jpg',
    //     'category_keywords' => ['Hukum'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Arahan Kapolri di Rakernis Korlantas: Wujudkan Mudik Aman hingga Tingkatkan Pelayanan',
        'content' => 'BANDUNG - Kapolri Jenderal Listyo Sigit Prabowo membuka Rapat Kerja Teknis (Rakernis) Korps Lalu Lintas (Korlantas) Polri di Bandung, Jawa Barat.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/14/337/2780906/arahan-kapolri-di-rakernis-korlantas-wujudkan-mudik-aman-hingga-tingkatkan-pelayanan-HeVzf2QWOX.jpg?w=300',
        'category_keywords' => ['Nasional'],
        'location' => ['city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'latitude' => -6.9175, 'longitude' => 107.6191]
    ],
    [
        'title' => 'Imbas Kasus Rafael Alun, Sri Mulyani Titipkan 3 Pesan Penting ke Pejabat Baru Kemenkeu',
        'content' => 'JAKARTA - Menteri Keuangan (Menkeu) Sri Mulyani Indrawati melantik 2 pejabat Pimpinan Tinggi Madya dan menitipkan tiga pesan penting kepada para pejabat yang baru dilantik.',
        'status' => 'Pending',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/17/320/2783205/imbas-kasus-rafael-alun-sri-mulyani-titipkan-3-pesan-penting-ke-pejabat-baru-kemenkeu-BBYX6Ngjox.jfif?w=300',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Cak Imin Terima Kunjungan Yusril Dkk, Ajak Lawan Kecurangan di Pemilu',
        'content' => 'Setelah mengunjungi PPP, Ketua Umum Partai Bulan Bintang (PBB) Yusril Ihza Mahendra kali ini ke kantor DPP Partai Kebangkitan Bangsa (PKB), Jakarta.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvmtmjqxk7ghytfkyk5wvqpb.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Perry Warjiyo: Indonesia Jadi Negara Maju pada 2047',
    //     'content' => 'Jakarta, CNBC Indonesia - Harapan Indonesia untuk menjadi negara maju masih mungkin terealisasi. Bank Indonesia (BI) memperkirakan level tersebut akan dicapai pada 2047 mendatang.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/20/fit-and-proper-test-perry-warjiyo-sebagai-calon-gubernur-bank-indonesia-bi-di-komisi-xi-dpr-senin-2032023-cnbc-indonesiamuhamm-1_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Hasto: Pemilu Tertutup Sesuai dengan Kultur, Terbuka Mengejar Popularitas',
        'content' => 'Sekjen PDIP Hasto Kristiyanto kembali menyinggung gugatan terhadap sistem Pemilu yang masih berjalan di Mahkamah Konstitusi. Ia mendukung sistem proporsional tertutup.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvz66khr4jq1aew8h7yaa7n8.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Dirut Pertamina Akui Tak Hanya Depo Plumpang yang Harus Benahi Buffer Zone',
        'content' => 'Direktur Utama PT Pertamina (Persero), mengungkapkan, tidak hanya jarak aman atau buffer zone Depo Plumpang, Jakarta Utara, yang harus dibenahi karena semakin dipadati warga.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gtpf5erzzf85gr66hw7jtq2c.jpg',
        'category_keywords' => ['Ekonomi'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    [
        'title' => 'Ulasan Lengkap tentang Perbedaan antara Tugas Tutorial dan UAS',
        'content' => 'Tugas tutorial dan UAS merupakan dua hal yang akan selalu dijumpai oleh mahasiswa. Keduanya tampak serupa sebab sama-sama memiliki bobot nilai, namun memiliki perbedaan mendasar.',
        'status' => 'Pending',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvzd83yjs1v4jkv7sh4ew4y0.jpg',
        'category_keywords' => ['Pendidikan'],
        'location' => ['city' => 'Tidak disebutkan', 'province' => 'Tidak disebutkan', 'latitude' => null, 'longitude' => null]
    ],
    [
        'title' => 'Kejagung Deteksi Aliran Dana Jhonny G Plate ke Adiknya dari Uang Rp534 Juta yang Dikembalikan',
        'content' => 'JAKARTA - Menteri Komunikasi dan Informatika (Menkominfo) Jhonny G Plate tengah diperiksa Kejaksaan Agung (Kejagung) terkait dugaan korupsi BAKTI Kominfo.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://img.okezone.com/dynamic/content/2023/03/15/337/2781430/kejagung-deteksi-aliran-dana-jhonny-g-plate-ke-adiknya-dari-uang-rp534-juta-yang-dikembalikan-6uLNqcn4qi.jpg?w=300',
        'category_keywords' => ['Hukum'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'Deretan Startup Dunia yang Jadi Nasabah Silicon Valley Bank',
    //     'content' => 'TEMPO.CO, Jakarta - Silicon Valley Bank (SVB) telah mengibarkan bendera putih setelah gagal menghimpun dana untuk menopang neraca keuangan. Banyak startup dunia yang menjadi nasabahnya.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/03/15/id_1188999/1188999_720.jpg',
    //     'category_keywords' => ['Teknologi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Tangis Keluarga Korban Kanjuruhan saat Vonis Bebas Terdakwa Polisi',
    //     'content' => 'Keluarga korban Tragedi Kanjuruhan yang hadir di sidang vonis dua polisi terdakwa, mengaku kecewa dengan putusan majelis hakim yang membebaskan salah satu terdakwa.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/16/keluarga-korban-kanjuruhan-menangis-dengar-putusan-bebas-terdakwa-kanjuruhan-1_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Hukum'],
    //     'location' => ['city' => 'Kabupaten Malang', 'province' => 'Jawa Timur', 'latitude' => -8.1654, 'longitude' => 112.6324]
    // ],
    [
        'title' => 'Golkar Bela Ridwan Kamil soal \'Maneh\'',
        'content' => 'Ketua DPD Golkar Jawa Barat, Ace Hasan Syadzily menanggapi polemik pemecatan guru di Cirebon, Muhammad Sabil, usai berkomentar \'maneh\' di akun media sosial Gubernur Jawa Barat.',
        'status' => 'Draft',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gq2gdt4tbf10z1jefjgy9ds3.jpg',
        'category_keywords' => ['Politik'],
        'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    ],
    // [
    //     'title' => 'BI Turunkan Senjata \'Operasi Kembar\' Jaga Rupiah',
    //     'content' => 'Jakarta, CNBC Indonesia - Dalam rangka menjaga nilai tukar rupiah di tengah gejolak krisis perbankan di luar negeri, Bank Indonesia (BI) akan menggalakkan operasi kembar.',
    //     'status' => 'Pending',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/16/pengumuman-hasil-rapat-dewan-gubernur-bulanan-bulan-maret-2023-2_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Ekonomi'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'Pemerintah Daerah Raih UHC Award 2023, Termasuk Kabupaten Terluar dan Perbatasan Indonesia',
    //     'content' => 'INFO NASIONAL - Sebanyak 22 Provinsi, 334 Kabupaten dan Kota mendapatkan penghargaan dari Pemerintah Indonesia atas keberhasilannya mewujudkan Cakupan Kesehatan Semesta (UHC).',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/03/14/id_1188809/1188809_720.jpg',
    //     'category_keywords' => ['Nasional'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    [
        'title' => 'Terima Penghargaan UHC, Bupati Tangerang: Peserta JKN Capai 99% di Akhir 2023',
        'content' => 'Pemerintah Kabupaten (Pemkab) Tangerang menerima penghargaan cakupan kesehatan semesta atau Universal Health Coverage (UHC) dari Pemerintah Pusat.',
        'status' => 'Published',
        'views' => rand(150, 2000),
        'image_url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvfrqskwdbfgvesgqpgspqh6.jpg',
        'category_keywords' => ['Kesehatan'],
        'location' => ['city' => 'Kabupaten Tangerang', 'province' => 'Banten', 'latitude' => -6.2704, 'longitude' => 106.4925]
    ],
    // [
    //     'title' => 'Kejaksaan Agung Serahkan Aset Sitaan Kasus Jiwasraya Rp 3,1 Triliun ke Erick Thohir',
    //     'content' => 'TEMPO.CO, Jakarta - Kejaksaan Agung menyerahkan aset sitaan dalam kasus korupsi PT Jiwasraya yang melibatkan Benny Tjokrosaputro cs senilai Rp 3,1 triliun ke Kementerian BUMN.',
    //     'status' => 'Published',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://statik.tempo.co/data/2023/03/06/id_1186543/1186543_720.jpg',
    //     'category_keywords' => ['Hukum'],
    //     'location' => ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456]
    // ],
    // [
    //     'title' => 'PDIP Jatim Tolak Timnas Israel: Solidaritas Perjuangan Palestina',
    //     'content' => 'Dewan Pimpinan Daerah PDIP Jawa Timur menolak kehadiran tim nasional Israel yang bakal mengikuti ajang Piala Dunia U-20 di Indonesia sebagai bentuk solidaritas pada Palestina.',
    //     'status' => 'Draft',
    //     'views' => rand(150, 2000),
    //     'image_url' => 'https://akcdn.detik.net/id/visual/2023/03/18/said-abdullah_169.jpeg?w=360&q=90',
    //     'category_keywords' => ['Politik'],
    //     'location' => ['city' => 'Surabaya', 'province' => 'Jawa Timur', 'latitude' => -7.2575, 'longitude' => 112.7521]
    // ]
];

        // Mapping kategori berdasarkan kata kunci dalam judul
        $categoryMapping = [
            'Teknologi' => ['Teknologi', 'AI', 'Digital', 'Startup', 'Fintech', 'Satelit', 'IoT'],
            'Olahraga' => ['Tim Nasional', 'Sepak bola', 'Turnamen', 'Badminton', 'Asian Games', 'Panjat Tebing', 'BWF'],
            'Ekonomi' => ['Ekonomi', 'UMKM', 'Investasi', 'Pendanaan', 'Ekspor', 'Bank Indonesia', 'FDI', 'Kartu Prakerja'],
            'Kesehatan' => ['Kesehatan', 'Telemedicine', 'Vaksin', 'Rumah Sakit', 'SIKDINAS'],
            'Pendidikan' => ['Pendidikan', 'Siswa', 'Universitas', 'Merdeka Belajar', 'Beasiswa'],
            'Politik' => ['Pemerintah', 'Kebijakan', 'Pelayanan Publik', 'Birokrasi', 'IKN', 'Gempa', 'Festival', 'Budaya', 'Politik'],
            'Hukum' => ['Hukum', 'Polisi', 'Kejaksaan', 'Pengadilan', 'Korupsi'],
            'Bencana' => ['Bencana', 'Gempa', 'Erupsi', 'Merapi', 'Evakuasi'],
            'Gaya Hidup' => ['Gaya Hidup', 'Lifestyle', 'Kota Terbahagia'],
            'Hiburan' => ['Hiburan', 'Artis', 'Selebriti', 'Film', 'Musik'],
            'Otomotif' => ['Otomotif', 'Mobil', 'Motor', 'Kendaraan'],
            'Nasional' => ['Nasional', 'Indonesia', 'Pemerintah'],
            'Internasional' => ['Internasional', 'Luar Negeri', 'Global'],
        ];

        foreach ($newsData as $index => $news) {
            // Tentukan kategori berdasarkan judul atau category_keywords
            $categoryId = $categoryIds[0]; // default ke kategori pertama
            
            // Cek category_keywords dulu jika ada
            if (isset($news['category_keywords'])) {
                foreach ($categoryMapping as $categoryName => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (in_array($keyword, $news['category_keywords'])) {
                            $category = Category::where('name', $categoryName)->first();
                            if ($category) {
                                $categoryId = $category->id;
                                break 2;
                            }
                        }
                    }
                }
            } else {
                // Fallback ke pengecekan berdasarkan judul
                foreach ($categoryMapping as $categoryName => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (str_contains($news['title'], $keyword)) {
                            $category = Category::where('name', $categoryName)->first();
                            if ($category) {
                                $categoryId = $category->id;
                                break 2;
                            }
                        }
                    }
                }
            }

            // Download gambar jika URL tersedia
            $imageName = null;
            if (isset($news['image_url'])) {
                $imageName = 'news_' . ($index + 1) . '_' . time() . '.jpg';
                // $downloadedImage = $this->downloadImage($news['image_url'], $imageName);
                
                // if (!$downloadedImage) {
                //     $imageName = null;
                //     $this->command->warn("Gagal mendownload gambar untuk berita: " . $news['title']);
                // } else {
                //     $this->command->info("Berhasil mendownload gambar untuk berita: " . $news['title']);
                // }
            }
            
            // Gunakan lokasi dari data berita jika ada, atau pilih secara acak
            $location = isset($news['location']) ? $news['location'] : $indonesianLocations[array_rand($indonesianLocations)];

            News::create([
                'title' => $news['title'],
                'content' => $news['content'],
                'status' => $news['status'],
                'views' => $news['views'],
                'user_id' => $writers->random()->id,
                'category_id' => $categoryId,
                'image' => $imageName,
                'city' => $location['city'],
                'province' => $location['province'],
                'country' => 'Indonesia',
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ]);
        }

        $this->command->info('News seeder berhasil dijalankan!');
    }
}
