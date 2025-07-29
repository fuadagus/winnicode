<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    /**
     * Data provinsi dan kota di Indonesia
     */
    private function getIndonesiaLocations()
    {
        return [
            'Aceh' => ['Banda Aceh', 'Langsa', 'Lhokseumawe', 'Meulaboh', 'Sabang'],
            'Sumatera Utara' => ['Medan', 'Binjai', 'Pematangsiantar', 'Sibolga', 'Tanjungbalai', 'Tebing Tinggi'],
            'Sumatera Barat' => ['Padang', 'Bukittinggi', 'Padang Panjang', 'Pariaman', 'Payakumbuh', 'Sawahlunto', 'Solok'],
            'Riau' => ['Pekanbaru', 'Dumai'],
            'Kepulauan Riau' => ['Tanjung Pinang', 'Batam'],
            'Jambi' => ['Jambi', 'Sungai Penuh'],
            'Bengkulu' => ['Bengkulu'],
            'Sumatera Selatan' => ['Palembang', 'Lubuklinggau', 'Pagar Alam', 'Prabumulih'],
            'Kepulauan Bangka Belitung' => ['Pangkal Pinang'],
            'Lampung' => ['Bandar Lampung', 'Metro'],
            'DKI Jakarta' => ['Jakarta Barat', 'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Utara'],
            'Jawa Barat' => ['Bandung', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Depok', 'Sukabumi', 'Tasikmalaya', 'Banjar'],
            'Jawa Tengah' => ['Semarang', 'Magelang', 'Pekalongan', 'Purwokerto', 'Salatiga', 'Solo', 'Tegal'],
            'D.I. Yogyakarta' => ['Yogyakarta'],
            'Jawa Timur' => ['Surabaya', 'Batu', 'Blitar', 'Kediri', 'Madiun', 'Malang', 'Mojokerto', 'Pasuruan', 'Probolinggo'],
            'Banten' => ['Serang', 'Cilegon', 'Tangerang', 'Tangerang Selatan'],
            'Bali' => ['Denpasar'],
            'Nusa Tenggara Barat' => ['Mataram', 'Bima'],
            'Nusa Tenggara Timur' => ['Kupang'],
            'Kalimantan Barat' => ['Pontianak', 'Singkawang'],
            'Kalimantan Tengah' => ['Palangkaraya'],
            'Kalimantan Selatan' => ['Banjarmasin', 'Banjarbaru'],
            'Kalimantan Timur' => ['Samarinda', 'Balikpapan', 'Bontang'],
            'Kalimantan Utara' => ['Tarakan'],
            'Sulawesi Utara' => ['Manado', 'Bitung', 'Kotamobagu', 'Tomohon'],
            'Sulawesi Tengah' => ['Palu'],
            'Sulawesi Selatan' => ['Makassar', 'Palopo', 'Parepare'],
            'Sulawesi Tenggara' => ['Kendari', 'Bau-Bau'],
            'Gorontalo' => ['Gorontalo'],
            'Sulawesi Barat' => ['Mamuju'],
            'Maluku' => ['Ambon', 'Tual'],
            'Maluku Utara' => ['Ternate', 'Tidore Kepulauan'],
            'Papua' => ['Jayapura'],
            'Papua Barat' => ['Manokwari', 'Sorong'],
            'Papua Selatan' => ['Merauke'],
            'Papua Tengah' => ['Nabire'],
            'Papua Pegunungan' => ['Jayawijaya'],
            'Papua Barat Daya' => ['Raja Ampat']
        ];
    }

    /**
     * Get all provinces
     */
    public function getProvinces()
    {
        $locations = $this->getIndonesiaLocations();
        $provinces = array_keys($locations);
        
        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }

    /**
     * Get cities by province
     */
    public function getCities(Request $request)
    {
        $province = $request->get('province');
        $locations = $this->getIndonesiaLocations();
        
        if (!isset($locations[$province])) {
            return response()->json([
                'success' => false,
                'message' => 'Province not found'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $locations[$province]
        ]);
    }

    /**
     * Get coordinates for a specific city
     */
    public function getCityCoordinates(Request $request)
    {
        $province = $request->get('province');
        $city = $request->get('city');
        
        if (!$province || !$city) {
            return response()->json([
                'success' => false,
                'message' => 'Province and city are required'
            ]);
        }
        
        $coordinates = $this->findCityCoordinates($province, $city);
        
        if (!$coordinates) {
            return response()->json([
                'success' => false,
                'message' => 'Coordinates not found for this city'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
                'city' => $city,
                'province' => $province
            ]
        ]);
    }

    /**
     * Reverse geocoding - convert coordinates to address
     */
    public function reverseGeocode(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        
        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'Latitude and longitude are required'
            ]);
        }

        try {
            // Menggunakan OpenStreetMap Nominatim untuk reverse geocoding
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 10,
                'addressdetails' => 1,
                'accept-language' => 'id,en'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['address'])) {
                    $address = $data['address'];
                    
                    // Extract city and province from the response
                    $city = $address['city'] ?? 
                           $address['town'] ?? 
                           $address['municipality'] ?? 
                           $address['village'] ?? 
                           $address['suburb'] ?? 
                           $address['district'] ?? null;
                    
                    $province = $address['state'] ?? 
                               $address['province'] ?? null;
                    
                    // Mapping beberapa nama provinsi yang sering berbeda
                    $provinceMapping = [
                        'Jakarta' => 'DKI Jakarta',
                        'Special Capital Region of Jakarta' => 'DKI Jakarta',
                        'West Java' => 'Jawa Barat',
                        'Central Java' => 'Jawa Tengah',
                        'East Java' => 'Jawa Timur',
                        'Special Region of Yogyakarta' => 'D.I. Yogyakarta',
                        'North Sumatra' => 'Sumatera Utara',
                        'West Sumatra' => 'Sumatera Barat',
                        'South Sumatra' => 'Sumatera Selatan',
                        'South Sulawesi' => 'Sulawesi Selatan',
                        'North Sulawesi' => 'Sulawesi Utara',
                        'Central Sulawesi' => 'Sulawesi Tengah',
                        'West Kalimantan' => 'Kalimantan Barat',
                        'East Kalimantan' => 'Kalimantan Timur',
                        'South Kalimantan' => 'Kalimantan Selatan',
                        'Central Kalimantan' => 'Kalimantan Tengah',
                        'West Nusa Tenggara' => 'Nusa Tenggara Barat',
                        'East Nusa Tenggara' => 'Nusa Tenggara Timur',
                    ];
                    
                    if ($province && isset($provinceMapping[$province])) {
                        $province = $provinceMapping[$province];
                    }
                    
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'city' => $city,
                            'province' => $province,
                            'country' => 'Indonesia',
                            'full_address' => $data['display_name'] ?? null
                        ]
                    ]);
                }
            }
            
            // Fallback jika reverse geocoding gagal - estimasi berdasarkan koordinat terdekat
            return $this->estimateLocationByCoordinates($lat, $lng);
            
        } catch (\Exception $e) {
            // Fallback ke estimasi berdasarkan koordinat
            return $this->estimateLocationByCoordinates($lat, $lng);
        }
    }

    /**
     * Find coordinates for a specific city
     */
    private function findCityCoordinates($province, $city)
    {
        // Comprehensive coordinate data for Indonesian cities
        $coordinates = [
            'DKI Jakarta' => [
                'Jakarta Barat' => ['latitude' => -6.1352, 'longitude' => 106.8133],
                'Jakarta Pusat' => ['latitude' => -6.1745, 'longitude' => 106.8227],
                'Jakarta Selatan' => ['latitude' => -6.2615, 'longitude' => 106.8106],
                'Jakarta Timur' => ['latitude' => -6.2250, 'longitude' => 106.9004],
                'Jakarta Utara' => ['latitude' => -6.1388, 'longitude' => 106.8650],
                'Kepulauan Seribu' => ['latitude' => -5.8719, 'longitude' => 106.5640],
            ],
            'Jawa Barat' => [
                'Bandung' => ['latitude' => -6.9175, 'longitude' => 107.6191],
                'Bekasi' => ['latitude' => -6.2349, 'longitude' => 106.9896],
                'Bogor' => ['latitude' => -6.5971, 'longitude' => 106.8060],
                'Cirebon' => ['latitude' => -6.7063, 'longitude' => 108.5570],
                'Depok' => ['latitude' => -6.4025, 'longitude' => 106.7942],
                'Sukabumi' => ['latitude' => -6.9175, 'longitude' => 106.9266],
                'Tasikmalaya' => ['latitude' => -7.3506, 'longitude' => 108.2172],
                'Karawang' => ['latitude' => -6.3015, 'longitude' => 107.3020],
                'Garut' => ['latitude' => -7.2253, 'longitude' => 107.8986],
                'Purwakarta' => ['latitude' => -6.5569, 'longitude' => 107.4431],
            ],
            'Jawa Tengah' => [
                'Semarang' => ['latitude' => -6.9667, 'longitude' => 110.4167],
                'Solo' => ['latitude' => -7.5667, 'longitude' => 110.8167],
                'Magelang' => ['latitude' => -7.4697, 'longitude' => 110.2176],
                'Pekalongan' => ['latitude' => -6.8886, 'longitude' => 109.6753],
                'Tegal' => ['latitude' => -6.8694, 'longitude' => 109.1402],
                'Purwokerto' => ['latitude' => -7.4197, 'longitude' => 109.2344],
                'Salatiga' => ['latitude' => -7.3314, 'longitude' => 110.5078],
                'Kudus' => ['latitude' => -6.8048, 'longitude' => 110.8405],
            ],
            'D.I. Yogyakarta' => [
                'Yogyakarta' => ['latitude' => -7.7956, 'longitude' => 110.3695],
                'Sleman' => ['latitude' => -7.7326, 'longitude' => 110.3553],
                'Bantul' => ['latitude' => -7.8753, 'longitude' => 110.3261],
                'Kulon Progo' => ['latitude' => -7.8291, 'longitude' => 110.1611],
                'Gunung Kidul' => ['latitude' => -7.9344, 'longitude' => 110.5906],
            ],
            'Jawa Timur' => [
                'Surabaya' => ['latitude' => -7.2575, 'longitude' => 112.7521],
                'Malang' => ['latitude' => -7.9797, 'longitude' => 112.6304],
                'Kediri' => ['latitude' => -7.8167, 'longitude' => 112.0167],
                'Blitar' => ['latitude' => -8.0985, 'longitude' => 112.1681],
                'Jember' => ['latitude' => -8.1844, 'longitude' => 113.7068],
                'Madiun' => ['latitude' => -7.6298, 'longitude' => 111.5239],
                'Mojokerto' => ['latitude' => -7.4664, 'longitude' => 112.4336],
                'Pasuruan' => ['latitude' => -7.6453, 'longitude' => 112.9075],
                'Probolinggo' => ['latitude' => -7.7543, 'longitude' => 113.2159],
                'Banyuwangi' => ['latitude' => -8.2192, 'longitude' => 114.3691],
            ],
            'Sumatera Utara' => [
                'Medan' => ['latitude' => 3.5952, 'longitude' => 98.6722],
                'Binjai' => ['latitude' => 3.6000, 'longitude' => 98.4855],
                'Tebing Tinggi' => ['latitude' => 3.3285, 'longitude' => 99.1625],
                'Pematangsiantar' => ['latitude' => 2.9597, 'longitude' => 99.0687],
                'Tanjungbalai' => ['latitude' => 2.9675, 'longitude' => 99.7975],
                'Sibolga' => ['latitude' => 1.7425, 'longitude' => 98.7794],
                'Padangsidimpuan' => ['latitude' => 1.3667, 'longitude' => 99.2667],
            ],
            'Sumatera Barat' => [
                'Padang' => ['latitude' => -0.9471, 'longitude' => 100.4172],
                'Bukittinggi' => ['latitude' => -0.3051, 'longitude' => 100.3693],
                'Payakumbuh' => ['latitude' => -0.2297, 'longitude' => 100.6339],
                'Solok' => ['latitude' => -0.7917, 'longitude' => 100.6539],
                'Sawahlunto' => ['latitude' => -0.6814, 'longitude' => 100.7814],
                'Padangpanjang' => ['latitude' => -0.4667, 'longitude' => 100.4000],
                'Pariaman' => ['latitude' => -0.6167, 'longitude' => 100.1167],
            ],
            'Sumatera Selatan' => [
                'Palembang' => ['latitude' => -2.9761, 'longitude' => 104.7754],
                'Prabumulih' => ['latitude' => -3.4333, 'longitude' => 104.2333],
                'Pagar Alam' => ['latitude' => -4.0333, 'longitude' => 103.2333],
                'Lubuklinggau' => ['latitude' => -3.3000, 'longitude' => 102.8667],
            ],
            'Bali' => [
                'Denpasar' => ['latitude' => -8.6500, 'longitude' => 115.2167],
                'Singaraja' => ['latitude' => -8.1120, 'longitude' => 115.0882],
                'Tabanan' => ['latitude' => -8.5439, 'longitude' => 115.1265],
                'Gianyar' => ['latitude' => -8.5439, 'longitude' => 115.3289],
                'Ubud' => ['latitude' => -8.5069, 'longitude' => 115.2625],
                'Sanur' => ['latitude' => -8.6833, 'longitude' => 115.2667],
                'Kuta' => ['latitude' => -8.7167, 'longitude' => 115.1667],
                'Nusa Dua' => ['latitude' => -8.8000, 'longitude' => 115.2333],
            ],
            'Sulawesi Selatan' => [
                'Makassar' => ['latitude' => -5.1477, 'longitude' => 119.4327],
                'Parepare' => ['latitude' => -4.0139, 'longitude' => 119.6269],
                'Palopo' => ['latitude' => -2.9917, 'longitude' => 120.1986],
                'Watampone' => ['latitude' => -4.5378, 'longitude' => 120.3289],
            ],
            'Sulawesi Utara' => [
                'Manado' => ['latitude' => 1.4748, 'longitude' => 124.8421],
                'Bitung' => ['latitude' => 1.4440, 'longitude' => 125.1825],
                'Tomohon' => ['latitude' => 1.3319, 'longitude' => 124.8378],
                'Kotamobagu' => ['latitude' => 0.7167, 'longitude' => 124.3167],
            ],
            'Kalimantan Timur' => [
                'Samarinda' => ['latitude' => -0.5017, 'longitude' => 117.1536],
                'Balikpapan' => ['latitude' => -1.2379, 'longitude' => 116.8529],
                'Bontang' => ['latitude' => 0.1392, 'longitude' => 117.4728],
                'Berau' => ['latitude' => 2.1667, 'longitude' => 117.3667],
            ],
            'Kalimantan Barat' => [
                'Pontianak' => ['latitude' => -0.0263, 'longitude' => 109.3425],
                'Singkawang' => ['latitude' => 0.9063, 'longitude' => 108.9816],
            ],
            'Kalimantan Selatan' => [
                'Banjarmasin' => ['latitude' => -3.3194, 'longitude' => 114.5906],
                'Banjarbaru' => ['latitude' => -3.4441, 'longitude' => 114.8519],
            ],
            'Riau' => [
                'Pekanbaru' => ['latitude' => 0.5071, 'longitude' => 101.4478],
                'Dumai' => ['latitude' => 1.6667, 'longitude' => 101.4500],
            ],
            'Jambi' => [
                'Jambi' => ['latitude' => -1.6101, 'longitude' => 103.6131],
                'Sungai Penuh' => ['latitude' => -2.0667, 'longitude' => 101.3833],
            ],
            'Lampung' => [
                'Bandar Lampung' => ['latitude' => -5.4292, 'longitude' => 105.2610],
                'Metro' => ['latitude' => -5.1133, 'longitude' => 105.3067],
            ],
            'Nusa Tenggara Barat' => [
                'Mataram' => ['latitude' => -8.5833, 'longitude' => 116.1167],
                'Bima' => ['latitude' => -8.4667, 'longitude' => 118.7167],
            ],
            'Nusa Tenggara Timur' => [
                'Kupang' => ['latitude' => -10.1667, 'longitude' => 123.5833],
                'Ende' => ['latitude' => -8.8333, 'longitude' => 121.6667],
                'Maumere' => ['latitude' => -8.6167, 'longitude' => 122.2167],
            ],
        ];
        
        if (isset($coordinates[$province][$city])) {
            return $coordinates[$province][$city];
        }
        
        // If exact city not found, return estimated coordinates based on province
        return $this->estimateCoordinatesByProvince($province);
    }

    /**
     * Estimate coordinates based on province center
     */
    private function estimateCoordinatesByProvince($province)
    {
        $provinceCoordinates = [
            'DKI Jakarta' => ['latitude' => -6.2088, 'longitude' => 106.8456],
            'Jawa Barat' => ['latitude' => -6.9175, 'longitude' => 107.6191],
            'Jawa Tengah' => ['latitude' => -7.2500, 'longitude' => 110.0000],
            'D.I. Yogyakarta' => ['latitude' => -7.7956, 'longitude' => 110.3695],
            'Jawa Timur' => ['latitude' => -7.5000, 'longitude' => 112.5000],
            'Sumatera Utara' => ['latitude' => 3.5952, 'longitude' => 98.6722],
            'Sumatera Barat' => ['latitude' => -0.9471, 'longitude' => 100.4172],
            'Sumatera Selatan' => ['latitude' => -2.9761, 'longitude' => 104.7754],
            'Bali' => ['latitude' => -8.3405, 'longitude' => 115.0920],
            'Sulawesi Selatan' => ['latitude' => -5.1477, 'longitude' => 119.4327],
            'Sulawesi Utara' => ['latitude' => 1.4748, 'longitude' => 124.8421],
            'Kalimantan Timur' => ['latitude' => -0.5017, 'longitude' => 117.1536],
            'Kalimantan Barat' => ['latitude' => -0.0263, 'longitude' => 109.3425],
            'Kalimantan Selatan' => ['latitude' => -3.3194, 'longitude' => 114.5906],
            'Riau' => ['latitude' => 0.5071, 'longitude' => 101.4478],
            'Jambi' => ['latitude' => -1.6101, 'longitude' => 103.6131],
            'Lampung' => ['latitude' => -5.4292, 'longitude' => 105.2610],
            'Nusa Tenggara Barat' => ['latitude' => -8.5833, 'longitude' => 116.1167],
            'Nusa Tenggara Timur' => ['latitude' => -10.1667, 'longitude' => 123.5833],
        ];
        
        return $provinceCoordinates[$province] ?? ['latitude' => -6.2088, 'longitude' => 106.8456];
    }

    /**
     * Estimate location based on coordinates when reverse geocoding fails
     */
    private function estimateLocationByCoordinates($lat, $lng)
    {
        // Data koordinat perkiraan untuk kota-kota besar Indonesia
        $majorCities = [
            ['name' => 'Jakarta', 'province' => 'DKI Jakarta', 'lat' => -6.2088, 'lng' => 106.8456],
            ['name' => 'Surabaya', 'province' => 'Jawa Timur', 'lat' => -7.2575, 'lng' => 112.7521],
            ['name' => 'Bandung', 'province' => 'Jawa Barat', 'lat' => -6.9175, 'lng' => 107.6191],
            ['name' => 'Medan', 'province' => 'Sumatera Utara', 'lat' => 3.5952, 'lng' => 98.6722],
            ['name' => 'Semarang', 'province' => 'Jawa Tengah', 'lat' => -6.9667, 'lng' => 110.4167],
            ['name' => 'Makassar', 'province' => 'Sulawesi Selatan', 'lat' => -5.1477, 'lng' => 119.4327],
            ['name' => 'Palembang', 'province' => 'Sumatera Selatan', 'lat' => -2.9761, 'lng' => 104.7754],
            ['name' => 'Yogyakarta', 'province' => 'D.I. Yogyakarta', 'lat' => -7.7956, 'lng' => 110.3695],
            ['name' => 'Denpasar', 'province' => 'Bali', 'lat' => -8.6500, 'lng' => 115.2167],
            ['name' => 'Balikpapan', 'province' => 'Kalimantan Timur', 'lat' => -1.2379, 'lng' => 116.8529],
            ['name' => 'Manado', 'province' => 'Sulawesi Utara', 'lat' => 1.4748, 'lng' => 124.8421],
            ['name' => 'Padang', 'province' => 'Sumatera Barat', 'lat' => -0.9471, 'lng' => 100.4172],
            ['name' => 'Malang', 'province' => 'Jawa Timur', 'lat' => -7.9797, 'lng' => 112.6304],
            ['name' => 'Samarinda', 'province' => 'Kalimantan Timur', 'lat' => -0.5017, 'lng' => 117.1536],
            ['name' => 'Banjarmasin', 'province' => 'Kalimantan Selatan', 'lat' => -3.3194, 'lng' => 114.5906],
        ];

        $closestCity = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($majorCities as $city) {
            $distance = $this->calculateDistance($lat, $lng, $city['lat'], $city['lng']);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestCity = $city;
            }
        }

        if ($closestCity && $minDistance < 200) { // dalam radius 200km
            return response()->json([
                'success' => true,
                'data' => [
                    'city' => $closestCity['name'],
                    'province' => $closestCity['province'],
                    'country' => 'Indonesia',
                    'estimated' => true,
                    'distance' => round($minDistance, 1) . ' km dari ' . $closestCity['name']
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to determine location from coordinates'
        ]);
    }

    /**
     * Calculate distance between two coordinates
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
