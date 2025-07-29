<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImageSeeder extends Seeder
{
    /**
     * Download image from URL and save to storage
     */
    private function downloadImage($url, $filename)
    {
        try {
            $this->command->info("Downloading: {$url}");
            
            // Tambahkan user agent dan headers untuk menghindari blocking
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9,id;q=0.8',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
                'Sec-Fetch-Dest' => 'image',
                'Sec-Fetch-Mode' => 'no-cors',
                'Sec-Fetch-Site' => 'cross-site',
                'Cache-Control' => 'max-age=0'
            ])->withoutVerifying()->withOptions([
                'decode_content' => false,
                'allow_redirects' => true
            ])->timeout(30)->get($url);
            
            if ($response->successful() && $response->header('content-type') && str_contains($response->header('content-type'), 'image')) {
                $imageContent = $response->body();
                
                // Pastikan direktori images ada
                if (!Storage::disk('public')->exists('images')) {
                    Storage::disk('public')->makeDirectory('images');
                }
                
                // Simpan gambar
                Storage::disk('public')->put('images/' . $filename, $imageContent);
                
                $this->command->info("✅ Successfully downloaded: {$filename}");
                return $filename;
            } else {
                $this->command->warn("❌ Failed to download (invalid response): {$url}");
                return null;
            }
        } catch (\Exception $e) {
            $this->command->error("❌ Error downloading {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Image Seeder...');
        
        // URL gambar dari data yang diberikan
       $newsImages = [
    [
        'title' => 'Begini Peran Anak Buah Wahyu Kenzo dalam Kasus Investasi Bodong',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/14/519/2780877/begini-peran-anak-buah-wahyu-kenzo-dalam-kasus-investasi-bodong-nlCzy5gTaS.jpg?w=300',
        'filename' => 'wahyu_kenzo_investasi_bodong.jpg'
    ],
    [
        'title' => 'Pengadilan Pakistan Minta Polisi Tunda Penangkapan Eks PM Imran Khan',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gfxweexxazrtnx3wqtejhr0k.jpg',
        'filename' => 'pakistan_imran_khan.jpg'
    ],
    [
        'title' => 'Sleman Mulai Siapkan Skenario Evakuasi Jika Eskalasi Erupsi Gunung Merapi Terus Meningkat',
        'url' => 'https://i.ytimg.com/vi/r9yWm3xoJkA/maxresdefault.jpg',
        'filename' => 'sleman_evakuasi_merapi.jpg'
    ],
    [
        'title' => 'Miras Milik Purnawirawan Polri Akan Dilimpahkan ke Polresta Tidore',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvqxrcs4wh5qkw0dxzeqb4r7.jpg',
        'filename' => 'miras_purnawirawan_polri.jpg'
    ],
    // [
    //     'title' => 'Serapan Beras Bulog Rendah, Pengamat: Pemerintah Bisa Wajibkan Penggilingan Setor ke Bulog',
    //     'url' => 'https://statik.tempo.co/data/2023/02/03/id_1178406/1178406_720.jpg',
    //     'filename' => 'serapan_beras_bulog.jpg'
    // ],
    [
        'title' => '4 Bandara di Yogyakarta dan Semarang Tak Terdampak Erupsi Merapi',
        'url' => 'https://bisnisnews.id/core/images/uploads/medium/gambar-20200327_ot58xu.jpg?w=648&h=340',
        'filename' => 'bandara_yogya_semarang_merapi.jpg'
    ],
    // [
    //     'title' => 'Sri Mulyani Segera Lancarkan Reformasi Jilid II Kemenkeu, Siapa yang Disasar?',
    //     'url' => 'https://statik.tempo.co/data/2023/02/25/id_1184113/1184113_720.jpg',
    //     'filename' => 'sri_mulyani_reformasi_kemenkeu.jpg'
    // ],
    [
        'title' => 'Bocah di Jambi Diancam & Dipaksa Oral Seks: Kini Trauma Tak Mau Sekolah',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1543320230/ua0ttd6awm5i6cdi79ov.jpg',
        'filename' => 'bocah_jambi_trauma.jpg'
    ],
    [
        'title' => 'Gegara Pimpinan DPR Tak Tanda Tangan, Mahfud MD dan PPATK Gagal Rapat Bareng Komisi III Bahas Transaksi Mencurigakan',
        'url' => 'https://media.suara.com/pictures/970x544/2022/09/06/57526-gedung-dpr-mpr-ri-ilustrasi-gedung-dpr-mpr-dpr-mpr.jpg',
        'filename' => 'mahfud_ppatk_dpr.jpg'
    ],
    // [
    //     'title' => 'Pertumbuhan Ekonomi Membaik Terdongkrak Konsumsi, Sri Mulyani Sebut Jokowi Akan Umumkan THR PNS',
    //     'url' => 'https://statik.tempo.co/data/2022/05/31/id_1114036/1114036_720.jpg',
    //     'filename' => 'ekonomi_membaik_thr_pns.jpg'
    // ],
    [
        'title' => '6 Perusahaan yang Ternyata Milik Indra Priawan Suami Nikita Willy',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/14/455/2780959/6-perusahaan-yang-ternyata-milik-indra-priawan-suami-nikita-willy-Olk4Q7DdJX.jpg?w=300',
        'filename' => 'indra_priawan_perusahaan.jpg'
    ],
    // [
    //     'title' => 'PPATK Klarifikasi soal Rp300 T di Kemenkeu: Bukan Korupsi Pegawai',
    //     'url' => 'https://akcdn.detik.net/id/visual/2022/07/06/keterangan-pers-terkait-aliran-dana-terlarang_169.jpeg?w=360&q=90',
    //     'filename' => 'ppatk_klarifikasi_300t.jpg'
    // ],
    [
        'title' => '4 Kota Terbahagia di Sulawesi Utara',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/17/320/2783127/4-kota-terbahagia-di-sulawesi-utara-fVq3WH3CEv.jpg?w=300',
        'filename' => 'kota_terbahagia_sulut.jpg'
    ],
    // [
    //     'title' => 'Harga Batu Bara Terjun Bebas, Bayan Resources Ungkap Strategi',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/15/optimisme-bisnis-batu-bara-kala-windfall-komoditas-berakhir-cnbc-indonesia-tv_169.png?w=360&q=90',
    //     'filename' => 'batu_bara_bayan_resources.jpg'
    // ],
    [
        'title' => 'Subaru WRX Wagon Jadi Rebutan, Inden hingga Akhir 2023',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gsjx7bvc6nwpwxep3w1wzjsa.jpg',
        'filename' => 'subaru_wrx_wagon.jpg'
    ],
    [
        'title' => 'Jurus Sri Mulyani Kumpulkan Influencer untuk Tangkis Kasus Kemenkeu, DPR Heran',
        'url' => 'https://media.suara.com/pictures/970x544/2023/03/21/94826-menteri-keuangan-menkeu-sri-mulyani-indrawati-antara.jpg',
        'filename' => 'sri_mulyani_influencer.jpg'
    ],
    [
        'title' => 'MinyaKita Langka di Pasaran, Zulhas Klaim Terlalu Sukses dan Banyak yang Cari',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01ga64pn6sv9xytdrxy7mnd4kf.jpg',
        'filename' => 'minyakita_langka_zulhas.jpg'
    ],
    [
        'title' => 'Hary Tanoesoedibjo Tekankan Pentingnya Adaptasi di Tengah Perkembangan Teknologi',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/16/320/2782259/hary-tanoesoedibjo-tekankan-pentingnya-adaptasi-di-tengah-perkembangan-teknologi-1QtZY8O3tR.jfif?w=300',
        'filename' => 'hary_tanoesoedibjo_teknologi.jpg'
    ],
    [
        'title' => 'Histeris Saat Tahu Nani Wijaya Meninggal, Connie Sutedja: Ikhlas, tapi Sedih',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvm5drhyyp3t452m3z018nbk.jpg',
        'filename' => 'nani_wijaya_meninggal.jpg'
    ],
    // [
    //     'title' => 'Frasa Gangguan Lainnya di UU Pemilu Digugat ke Mahkamah Konstitusi',
    //     'url' => 'https://statik.tempo.co/data/2019/05/12/id_841166/841166_720.jpg',
    //     'filename' => 'uu_pemilu_mahkamah_konstitusi.jpg'
    // ],
    // [
    //     'title' => 'Sah! Ini Jajaran Direksi dan Komisaris Baru Bank Mandiri',
    //     'url' => 'https://akcdn.detik.net/id/visual/2018/01/11/1ba89161-627b-4042-8e82-4a3318ba1686_169.jpeg?w=360&q=90',
    //     'filename' => 'direksi_komisaris_bank_mandiri.jpg'
    // ],
    // [
    //     'title' => 'Sri Mulyani Happy! 7,1 Juta Wajib Pajak Sudah Lapor SPT',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/02/28/menteri-keuangan-sri-mulyani-indrawati-dalam-economic-outlook-2023-dengan-tema-menjaga-momentum-ekonomi-di-tengah-ketidakpasti-24_169.jpeg?w=360&q=90',
    //     'filename' => 'sri_mulyani_lapor_spt.jpg'
    // ],
    [
        'title' => 'Beda Klaim Jokowi vs Polri Soal Impor Perlengkapan Polisi, Siapa yang Benar?',
        'url' => 'https://media.suara.com/pictures/970x544/2023/03/16/23985-jokowi-saat-marah.jpg',
        'filename' => 'jokowi_vs_polri_impor.jpg'
    ],
    [
        'title' => 'Tompi Dedikasikan Lagu Religi Terbarunya untuk Mendiang Ibu',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvzcygyp0ask622vdhesymch.jpg',
        'filename' => 'tompi_lagu_religi.jpg'
    ],
    [
        'title' => 'Bank Indonesia Catat Kredit Perbankan Tumbuh Jadi 10,64 Persen di Februari 2023',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1589880120/niu5sfxkdmf5jtiax9an.jpg',
        'filename' => 'bi_kredit_perbankan.jpg'
    ],
    // [
    //     'title' => 'Bus ALS Angkut 16 Penumpang Terbakar di Jalan Lintas Bukittinggi-Medan',
    //     'url' => 'https://akcdn.detik.net/id/visual/2021/11/26/bus-vaksin-terbakar-di-muara-enim-1_169.jpeg?w=360&q=90',
    //     'filename' => 'bus_als_terbakar.jpg'
    // ],
    [
        'title' => 'Harga BBM Non Subsidi Pertamina Naik Lagi Per Hari Ini, Ini Daftarnya',
        'url' => 'https://media.suara.com/pictures/970x544/2023/01/03/73935-harga-bbm-pertamina-pertamax-pertalite.jpg',
        'filename' => 'harga_bbm_pertamina_naik.jpg'
    ],
    // [
    //     'title' => 'Soal Transaksi Rp 300 T di Kemenkeu, Mahfud: Perkembangannya Positif',
    //     'url' => 'https://cdn-asset.jawapos.com/wp-content/uploads/2023/03/Menkopolhukam-Mahfud-MD-Dan-Wamenkeu-Suahasil-Nazara-Dery-Ridwansah-2-640x480.jpg',
    //     'filename' => 'mahfud_transaksi_300t_kemenkeu.jpg'
    // ],
    // [
    //     'title' => 'Guru Cirebon Dipecat Usai Komentar di IG RK Tak Niat Ajukan Gugatan',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/15/muhammad-sabil_169.jpeg?w=360&q=90',
    //     'filename' => 'guru_cirebon_dipecat_rk.jpg'
    // ],
    // [
    //     'title' => 'Gunung Merapi Jateng Masih Siaga! Begini Kondisi Terbarunya..',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/12/1248048203_169.jpeg?w=360&q=90',
    //     'filename' => 'gunung_merapi_siaga.jpg'
    // ],
    // [
    //     'title' => 'Aspri Wamenkumham Bakal Laporkan Balik Ketua IPW ke Bareskrim',
    //     'url' => 'https://akcdn.detik.net/id/visual/2022/08/25/sugeng-teguh-santoso-1_169.jpeg?w=360&q=90',
    //     'filename' => 'aspri_wamenkumham_lapor_ipw.jpg'
    // ],
    // [
    //     'title' => 'Harga Emas Amblas ke Rp1,054 Juta per Gram',
    //     'url' => 'https://akcdn.detik.net/id/visual/2018/11/12/e2edc0e5-f878-4478-946b-9b22048aaccf_169.jpeg?w=360&q=90',
    //     'filename' => 'harga_emas_amblas.jpg'
    // ],
    [
        'title' => 'Demo Anarkistis Tolak Reformasi Pensiun di Prancis: Balai Kota Bordeaux Dibakar',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gw62ch9yt4q85x0ntc0pj07v.jpg',
        'filename' => 'demo_prancis_reformasi_pensiun.jpg'
    ],
    // [
    //     'title' => 'Laporan Transaksi Janggal Rafael Alun Mangkrak Bertahun-tahun di KPK, Ini Alasannya',
    //     'url' => 'https://statik.tempo.co/data/2023/03/02/id_1185367/1185367_720.jpg',
    //     'filename' => 'transaksi_janggal_rafael_alun.jpg'
    // ],
    // [
    //     'title' => 'Menkeu AS dan Ketua The Fed Buka Suara Terkait Nasib SVB',
    //     'url' => 'https://akcdn.detik.net/id/visual/2019/07/23/40230572-4ed1-41f0-aaa8-b09a66c9af3f_169.jpeg?w=360&q=90',
    //     'filename' => 'menkeu_as_the_fed_svb.jpg'
    // ],
    [
        'title' => 'Mikel Arteta Raih Manager of the Year di London Football Awards',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01grhacpsn773t0xqtyekkahmy.jpg',
        'filename' => 'mikel_arteta_manager_of_year.jpg'
    ],
    // [
    //     'title' => 'FOTO: PA 212 Demo Tolak Kedatangan Timnas Israel',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/20/demo-tolak-kedatangan-timnas-israel-4_169.jpeg?w=360&q=90',
    //     'filename' => 'pa_212_demo_timnas_israel.jpg'
    // ],
    [
        'title' => 'Ketua MPR: Tak Ada Tawar-menawar, LGBT Harus Dilawan',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gf067dh1rfybv2sfkdmpadmm.jpg',
        'filename' => 'ketua_mpr_tolak_lgbt.jpg'
    ],
    // [
    //     'title' => 'Video: Tren Busana Lebaran, Motif Bunga Kecil Jadi Pilihan',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/20/cnbc-indonesia-tv-7_169.png?w=360&q=90',
    //     'filename' => 'tren_busana_lebaran_2023.jpg'
    // ],
    [
        'title' => 'Pengertian dari Sudut Deklinasi dalam Pembelajaran Matematika',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvce58pq9mwg2n5svxewwvqr.jpg',
        'filename' => 'pengertian_sudut_deklinasi.jpg'
    ],
    [
        'title' => 'Mahfud Respons Anies soal Ada Menko Mau Ubah Konstitusi: Tak Tahu, Tak Ada Itu',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gv9tgcbn11cz5kehw221cfd9.jpg',
        'filename' => 'mahfud_respons_anies_konstitusi.jpg'
    ],
    // [
    //     'title' => 'Hilirisasi Batu Bara Jokowi Terancam Gagal, Ini Pemicunya',
    //     'url' => 'https://akcdn.detik.net/id/visual/2021/11/22/aktivitas-bongkar-muat-batu-bara-10_169.jpeg?w=360&q=90',
    //     'filename' => 'hilirisasi_batu_bara_gagal.jpg'
    // ],
    [
        'title' => 'Kasus Mayat Mutilasi dalam Koper, Polisi Ungkap Perkiraan Waktu Kematian Korban',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/16/338/2782266/kasus-mayat-mutilasi-dalam-koper-polisi-ungkap-perkiraan-waktu-kematian-korban-TCFRRT83UC.jpg?w=300',
        'filename' => 'mayat_mutilasi_koper_bogor.jpg'
    ],
    // [
    //     'title' => '20 Negara Mau Jadi \'Sekutu\' Rusia, RI Salah Satunya',
    //     'url' => 'https://akcdn.detik.net/id/visual/2022/09/15/1182276215_169.jpeg?w=360&q=90',
    //     'filename' => 'sekutu_rusia_brics.jpg'
    // ],
    [
        'title' => '4 Orang dalam 1 Keluarga Tewas Gara-Gara Kepercayaan pada Teori Konspirasi',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/22/18/2785608/4-orang-dalam-1-keluarga-tewas-gara-gara-kepercayaan-pada-teori-konspirasi-FannNVl1OQ.jpg?w=300',
        'filename' => 'keluarga_tewas_teori_konspirasi.jpg'
    ],
    [
        'title' => 'Bos Pertamina Ungkap Awal Mula Warga Padati Lahan Depo Plumpang',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gtpf5erzzf85gr66hw7jtq2c.jpg',
        'filename' => 'warga_depo_plumpang.jpg'
    ],
    // [
    //     'title' => 'Pemuda Diminta Tampil Konstruktif, Artikulatif, dan Visioner Songsong Indonesia Emas',
    //     'url' => 'https://statik.tempo.co/data/2023/02/23/id_1183562/1183562_720.jpg',
    //     'filename' => 'pemuda_indonesia_emas_2045.jpg'
    // ],
    // [
    //     'title' => 'LPSK Cabut Perlindungan karena Richard Eliezer Langgar Kesepakatan',
    //     'url' => 'https://statik.tempo.co/data/2023/02/27/id_1184626/1184626_720.jpg',
    //     'filename' => 'lpsk_cabut_perlindungan_eliezer.jpg'
    // ],
    [
        'title' => 'Arahan Kapolri di Rakernis Korlantas: Wujudkan Mudik Aman hingga Tingkatkan Pelayanan',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/14/337/2780906/arahan-kapolri-di-rakernis-korlantas-wujudkan-mudik-aman-hingga-tingkatkan-pelayanan-HeVzf2QWOX.jpg?w=300',
        'filename' => 'arahan_kapolri_rakernis_korlantas.jpg'
    ],
    [
        'title' => 'Imbas Kasus Rafael Alun, Sri Mulyani Titipkan 3 Pesan Penting ke Pejabat Baru Kemenkeu',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/17/320/2783205/imbas-kasus-rafael-alun-sri-mulyani-titipkan-3-pesan-penting-ke-pejabat-baru-kemenkeu-BBYX6Ngjox.jfif?w=300',
        'filename' => 'sri_mulyani_pesan_pejabat_kemenkeu.jpg'
    ],
    [
        'title' => 'Cak Imin Terima Kunjungan Yusril Dkk, Ajak Lawan Kecurangan di Pemilu',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvmtmjqxk7ghytfkyk5wvqpb.jpg',
        'filename' => 'cak_imin_kunjungan_yusril.jpg'
    ],
    // [
    //     'title' => 'Perry Warjiyo: Indonesia Jadi Negara Maju pada 2047',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/20/fit-and-proper-test-perry-warjiyo-sebagai-calon-gubernur-bank-indonesia-bi-di-komisi-xi-dpr-senin-2032023-cnbc-indonesiamuhamm-1_169.jpeg?w=360&q=90',
    //     'filename' => 'perry_warjiyo_indonesia_maju.jpg'
    // ],
    [
        'title' => 'Hasto: Pemilu Tertutup Sesuai dengan Kultur, Terbuka Mengejar Popularitas',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvz66khr4jq1aew8h7yaa7n8.jpg',
        'filename' => 'hasto_pemilu_tertutup.jpg'
    ],
    [
        'title' => 'Dirut Pertamina Akui Tak Hanya Depo Plumpang yang Harus Benahi Buffer Zone',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gtpf5erzzf85gr66hw7jtq2c.jpg',
        'filename' => 'pertamina_benahi_buffer_zone.jpg'
    ],
    [
        'title' => 'Ulasan Lengkap tentang Perbedaan antara Tugas Tutorial dan UAS',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvzd83yjs1v4jkv7sh4ew4y0.jpg',
        'filename' => 'perbedaan_tutorial_dan_uas.jpg'
    ],
    [
        'title' => 'Kejagung Deteksi Aliran Dana Jhonny G Plate ke Adiknya dari Uang Rp534 Juta yang Dikembalikan',
        'url' => 'https://img.okezone.com/dynamic/content/2023/03/15/337/2781430/kejagung-deteksi-aliran-dana-jhonny-g-plate-ke-adiknya-dari-uang-rp534-juta-yang-dikembalikan-6uLNqcn4qi.jpg?w=300',
        'filename' => 'kejagung_aliran_dana_jhonny_plate.jpg'
    ],
    // [
    //     'title' => 'Deretan Startup Dunia yang Jadi Nasabah Silicon Valley Bank',
    //     'url' => 'https://statik.tempo.co/data/2023/03/15/id_1188999/1188999_720.jpg',
    //     'filename' => 'startup_nasabah_svb.jpg'
    // ],
    // [
    //     'title' => 'Tangis Keluarga Korban Kanjuruhan saat Vonis Bebas Terdakwa Polisi',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/16/keluarga-korban-kanjuruhan-menangis-dengar-putusan-bebas-terdakwa-kanjuruhan-1_169.jpeg?w=360&q=90',
    //     'filename' => 'keluarga_korban_kanjuruhan_vonis.jpg'
    // ],
    [
        'title' => 'Golkar Bela Ridwan Kamil soal \'Maneh\'',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gq2gdt4tbf10z1jefjgy9ds3.jpg',
        'filename' => 'golkar_bela_ridwan_kamil_maneh.jpg'
    ],
    // [
    //     'title' => 'BI Turunkan Senjata \'Operasi Kembar\' Jaga Rupiah',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/16/pengumuman-hasil-rapat-dewan-gubernur-bulanan-bulan-maret-2023-2_169.jpeg?w=360&q=90',
    //     'filename' => 'bi_operasi_kembar_rupiah.jpg'
    // ],
    // [
    //     'title' => 'Pemerintah Daerah Raih UHC Award 2023, Termasuk Kabupaten Terluar dan Perbatasan Indonesia',
    //     'url' => 'https://statik.tempo.co/data/2023/03/14/id_1188809/1188809_720.jpg',
    //     'filename' => 'pemda_raih_uhc_award_2023.jpg'
    // ],
    [
        'title' => 'Terima Penghargaan UHC, Bupati Tangerang: Peserta JKN Capai 99% di Akhir 2023',
        'url' => 'https://blue.kumparan.com/image/upload/fl_progressive,fl_lossy,c_fill,q_auto:best,w_480,f_jpg/v1634025439/01gvfrqskwdbfgvesgqpgspqh6.jpg',
        'filename' => 'bupati_tangerang_uhc_jkn.jpg'
    ],
    // [
    //     'title' => 'Kejaksaan Agung Serahkan Aset Sitaan Kasus Jiwasraya Rp 3,1 Triliun ke Erick Thohir',
    //     'url' => 'https://statik.tempo.co/data/2023/03/06/id_1186543/1186543_720.jpg',
    //     'filename' => 'kejagung_aset_sitaan_jiwasraya.jpg'
    // ],
    // [
    //     'title' => 'PDIP Jatim Tolak Timnas Israel: Solidaritas Perjuangan Palestina',
    //     'url' => 'https://akcdn.detik.net/id/visual/2023/03/18/said-abdullah_169.jpeg?w=360&q=90',
    //     'filename' => 'pdip_jatim_tolak_timnas_israel.jpg'
    // ]
];

        $successCount = 0;
        $failedCount = 0;
        
        foreach ($newsImages as $index => $imageData) {
            $this->command->info("Processing image " . ($index + 1) . "/" . count($newsImages) . ": " . $imageData['title']);
            
            $filename = $this->downloadImage($imageData['url'], $imageData['filename']);
            
            if ($filename) {
                $successCount++;
                
                // Update news record with the image filename
                $news = News::where('title', 'LIKE', '%' . substr($imageData['title'], 0, 50) . '%')->first();
                if ($news) {
                    $news->update(['image' => $filename]);
                    $this->command->info("✅ Updated news record with image: {$imageData['title']}");
                } else {
                    $this->command->warn("⚠️ News record not found for: {$imageData['title']}");
                }
            } else {
                $failedCount++;
            }
            
            // Add small delay to avoid overwhelming servers
            sleep(1);
        }
        
        $this->command->info("Image seeding completed!");
        $this->command->info("✅ Successfully downloaded: {$successCount} images");
        $this->command->info("❌ Failed downloads: {$failedCount} images");
        
        // Show storage info
        $storagePath = storage_path('app/public/images');
        if (is_dir($storagePath)) {
            $fileCount = count(glob($storagePath . '/*'));
            $this->command->info("📁 Total images in storage: {$fileCount}");
        }
    }
}
