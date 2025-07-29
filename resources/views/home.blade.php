@extends('layouts.app')

@section('content')
    <!-- Main Post Section Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-7 col-xl-8 mt-0">
                    @foreach ($latestNews->take(1) as $news)
                        <div class="position-relative overflow-hidden rounded" 
                             data-latitude="{{ $news->latitude }}" 
                             data-longitude="{{ $news->longitude }}">
                            <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                class="img-fluid rounded img-zoomin w-100" alt="" style="height: 475px" />
                            <div class="d-flex justify-content-center px-4 position-absolute flex-wrap"
                                style="bottom: 10px; left: 0">
                                <div class="text-white me-4"><i class="fa fa-clock"></i>
                                    {{ $news->created_at->translatedFormat('d F Y') }}</div>
                                <div class="text-white me-4"><i class="fa fa-eye"></i> {{ $news->views }}</div>
                                <div class="text-white me-4"><i class="fa fa-thumbs-up"></i> {{ $news->likes->count() }}
                                </div>
                                @if($news->city || $news->province)
                                    <div class="text-white me-4"><i class="fa fa-map-marker"></i> {{ $news->location }}</div>
                                @endif
                            </div>
                            
                        </div>
                        <div class="border-bottom py-3">
                            <a href="{{ route('news.show', $news->id) }}"
                                class="display-4 text-dark mb-0 link-hover">{{ $news->title }}</a>
                        </div>
                        <p class="mt-3 mb-4">
                            {!! Str::limit($news->content, 450, '...') !!}
                        </p>
                    @endforeach
                    @foreach ($popularNews->take(1) as $news)
                        <div class="bg-light p-4 rounded" 
                             data-latitude="{{ $news->latitude }}" 
                             data-longitude="{{ $news->longitude }}">
                            <div class="news-2">
                                <h3 class="mb-4">Top Views</h3>
                            </div>
                            <div class="row g-4 align-items-center">
                                <div class="col-md-6">
                                    <div class="rounded overflow-hidden">
                                        <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                            class="img-fluid rounded img-zoomin w-100" alt="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('news.show', $news->id) }}" class="h3">{{ $news->title }}</a>
                                        <p class="mb-0 fs-5 ms-1">
                                            <i class="fa fa-clock">
                                                {{ $news->created_at->translatedFormat('d F Y H:i') }}</i>
                                        </p>
                                        <p class="mb-0 fs-5 ms-1">
                                            <i class="fa fa-eye"> {{ $news->views }} Views</i>
                                        </p>
                                        <p class="mb-0 fs-5 ms-1">
                                            <i class="fa fa-thumbs-up">
                                                {{ $news->likes->count() }} Likes</i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 pt-0">
                        <div class="row g-4">
                            @foreach ($latestNews->skip(1)->take(1) as $news)
                                <div class="col-12" 
                                     data-latitude="{{ $news->latitude }}" 
                                     data-longitude="{{ $news->longitude }}">
                                    <div class="rounded overflow-hidden">
                                        <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                            class="img-fluid rounded img-zoomin w-100" alt="" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('news.show', $news->id) }}"
                                            class="h4 mb-2">{{ $news->title }}</a>
                                        <p class="fs-5 mb-0">
                                            <i class="fa fa-clock">
                                                {{ $news->created_at->translatedFormat('d F Y H:i') }}</i>
                                        </p>
                                        <p class="fs-5 mb-0">
                                            <i class="fa fa-eye"> {{ $news->views }}</i>
                                        </p>
                                        <p class="fs-5 mb-0">
                                            <i class="fa fa-thumbs-up">
                                                {{ $news->likes->count() }}</i>
                                        </p>
                                        @if($news->city || $news->province)
                                            <p class="fs-6 mb-0 news-location-info">
                                                <i class="fa fa-map-marker text-muted"></i> <span>{{ $news->location }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($latestNews->skip(2)->take(5) as $news)
                                <div class="col-12" 
                                     data-latitude="{{ $news->latitude }}" 
                                     data-longitude="{{ $news->longitude }}">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-5">
                                            <div class="overflow-hidden rounded">
                                                <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                                    class="img-zoomin img-fluid rounded w-100" alt="" />
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="features-content d-flex flex-column">
                                                <a href="{{ route('news.show', $news->id) }}"
                                                    class="h6">{{ $news->title }}</a>
                                                <small><i class="fa fa-clock">
                                                        {{ $news->created_at->translatedFormat('d F Y H:i') }} </i>
                                                </small>
                                                <small><i class="fa fa-eye"> {{ $news->views }}</i></small>
                                                <small>
                                                    <i class="fa fa-thumbs-up">
                                                        {{ $news->likes->count() }}</i>
                                                </small>
                                                @if($news->city || $news->province)
                                                    <small class="d-block news-location-info">
                                                        <i class="fa fa-map-marker text-muted"></i> <span>{{ $news->location }}</span>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Post Section End -->

    {{-- <!-- Banner Start -->
    <div class="container-fluid py-5 my-5"
        style="
    background: linear-gradient(
      rgba(202, 203, 185, 1),
      rgba(202, 203, 185, 1)
    );
  ">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mb-4" style="height: 80px;">
                    <h1 class="mb-4">Get Every Weekly Updates</h1>
                    <p class="text-dark mb-4 pb-2">
                        Lorem Ipsum is simply dummy text of the printing and typesetting
                        industry. Lorem Ipsum has been the industry's standard dummy text
                        ever since the 1500s, when an unknown printer took a galley
                    </p>
                    <div class="position-relative mx-auto">
                        <input class="form-control w-100 py-3 rounded-pill" type="email"
                            placeholder="Your Busines Email" />
                        <button type="submit"
                            class="btn btn-primary py-3 px-5 position-absolute rounded-pill text-white h-100"
                            style="top: 0; right: 0">
                            Subscribe Now
                        </button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="rounded">
                        <img src="{{ asset('th/img/banner-img.jpg') }}" class="img-fluid rounded w-100 rounded"
                            alt="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner End --> --}}

    <!-- Latest News Start -->
    <div class="container-fluid latest-news py-5">
        <div class="container py-5">
            <h2 class="mb-4">Latest News</h2>
            <div class="latest-news-carousel owl-carousel">
                @foreach ($latestNews as $news)
                    <div class="latest-news-item" 
                         data-latitude="{{ $news->latitude }}" 
                         data-longitude="{{ $news->longitude }}">
                        <div class="bg-light rounded">
                            <div class="rounded-top overflow-hidden">
                                <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                    class="img-zoomin img-fluid rounded-top w-100" alt="" />
                            </div>
                            <div class="d-flex flex-column p-4">
                                <a href="{{ route('news.show', $news->id) }}" class="h4">
                                    {{ Str::limit($news->title, 35, '...') }}</a>
                                <div class="d-flex justify-content-between">
                                    <small class="small text-body">{{ 'by ' . $news->author->name }}</small>
                                    <small class="text-body d-block"><i class="fas fa-calendar-alt me-1"></i>
                                        {{ $news->created_at->translatedFormat('j F Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Latest News End -->

    <!-- Most Populer News Start -->
    <div class="container-fluid populer-news py-5">
        <div class="container py-5">
            <div class="tab-class mb-4">
                <div class="row g-4">
                    <div class="col-lg-8 col-xl-9">
                        <div class="d-flex flex-column flex-md-row justify-content-md-between border-bottom mb-4">
                            <h1 class="mb-4">Trending Category</h1>
                            <ul class="nav nav-pills d-inline-flex text-center">
                                @foreach ($topCategory->take(3) as $key => $categories)
                                    <li class="nav-item mb-3">
                                        <a class="d-flex py-2 bg-light rounded-pill me-2" data-bs-toggle="pill"
                                            href="#tab-{{ $key + 1 }}">
                                            <span class="text-dark" style="width: 100px">{{ $categories->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="tab-content mb-4">
                            @foreach ($topCategory->take(3) as $key => $categories)
                                <div id="tab-{{ $key + 1 }}"
                                    class="tab-pane fade show p-0 {{ $key === 0 ? 'active' : '' }}">
                                    <div class="row g-4">
                                        @foreach ($categories->news()->where('status', 'Published')->withCount('likes')->orderBy('likes_count', 'desc')->take(1)->get() as $news)
                                            <div class="col-lg-8">
                                                <div class="position-relative rounded overflow-hidden">
                                                    <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                                        class="img-zoomin img-fluid rounded w-100" alt="" />
                                                    <div class="position-absolute text-white px-4 py-2 bg-primary rounded"
                                                        style="top: 20px; right: 20px">
                                                        {{ $categories->name }}
                                                    </div>
                                                </div>
                                                <div class="my-4">
                                                    <a href="{{ route('news.show', $news->id) }}"
                                                        class="h4">{{ $news->title }}</a>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="text-dark me-4"><i class="fa fa-clock"></i>
                                                        {{ $news->created_at->translatedFormat('d F Y') }}</div>
                                                    <div class="text-dark me-4"><i class="fa fa-eye"></i>
                                                        {{ $news->views }}</div>
                                                    <div class="text-dark me-4"><i class="fa fa-thumbs-up"></i>
                                                        {{ $news->likes->count() }}
                                                    </div>
                                                </div>
                                                <p class="my-4">
                                                    {!! Str::limit($news->content, 450, '...') !!}
                                                </p>
                                            </div>
                                        @endforeach
                                        <div class="col-lg-4">
                                            @foreach ($categories->news()->where('status', 'Published')->withCount('likes')->orderBy('likes_count', 'desc')->skip(1)->take(5)->get() as $news)
                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <div class="row g-4 align-items-center pb-4">
                                                            <div class="col-5">
                                                                <div class="overflow-hidden rounded">
                                                                    <img src="{{ $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg') }}"
                                                                        class="img-zoomin img-fluid rounded w-100"
                                                                        alt="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-7">
                                                                <div class="features-content d-flex flex-column">
                                                                    <p class="text-uppercase mb-2">{{ $categories->name }}
                                                                    </p>
                                                                    <a href="{{ route('news.show', $news->id) }}"
                                                                        class="h6">{{ $news->title }}</a>
                                                                    <small class="text-body d-block"><i
                                                                            class="fas fa-calendar-alt me-1"></i>
                                                                        {{ $news->created_at->translatedFormat('j F Y') }}
                                                                    </small>
                                                                    <small class="text-body d-block"><i
                                                                            class="fas fa-eye me-1"></i>
                                                                        {{ $news->views }}
                                                                    </small>
                                                                    <small class="text-body d-block"><i
                                                                            class="fas fa-thumbs-up me-1"></i>
                                                                        {{ $news->likes->count() }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-4 col-xl-3">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="p-3 rounded border">
                                    @component('components.col-2')
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Most Populer News End -->
@endsection
@section('custom-footer')
<script>
    // Test sederhana untuk geolocation
    function testGeolocation() {
        alert('Testing geolocation...');
        console.log('Testing geolocation started');
        
        if (navigator.geolocation) {
            console.log('Geolocation is supported');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // BERHASIL
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    alert(`Lokasi ditemukan!\nLatitude: ${lat}\nLongitude: ${lng}`);
                    console.log('Success:', lat, lng);
                    
                    // Cari berita dan tambahkan badge
                    addBadgesToNews(lat, lng);
                },
                function(error) {
                    // GAGAL
                    let msg = 'Error: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg += "User denied the request for Geolocation.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg += "Location information is unavailable.";
                            break;
                        case error.TIMEOUT:
                            msg += "The request to get user location timed out.";
                            break;
                        default:
                            msg += "An unknown error occurred.";
                            break;
                    }
                    alert(msg);
                    console.log('Error:', error);
                },
                {
                    enableHighAccuracy: false,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
            console.log('Geolocation not supported');
        }
    }
    
    // Fungsi untuk menambahkan badge ke berita
    function addBadgesToNews(userLat, userLng) {
        console.log('Adding badges to news...');
        
        // Cari semua elemen dengan data-latitude dan data-longitude
        const newsElements = document.querySelectorAll('[data-latitude][data-longitude]');
        console.log('Found news elements:', newsElements.length);
        
        newsElements.forEach(function(element, index) {
            const newsLat = parseFloat(element.getAttribute('data-latitude'));
            const newsLng = parseFloat(element.getAttribute('data-longitude'));
            
            console.log(`News ${index}: lat=${newsLat}, lng=${newsLng}`);
            
            if (!isNaN(newsLat) && !isNaN(newsLng)) {
                // Hitung jarak sederhana (tidak akurat tapi cukup untuk demo)
                const distance = Math.sqrt(
                    Math.pow(userLat - newsLat, 2) + Math.pow(userLng - newsLng, 2)
                ) * 111; // Konversi ke km (rough estimate)
                
                console.log(`Distance to news ${index}: ${distance.toFixed(1)} km`);
                
                // Jika jarak kurang dari 100km, tambahkan badge
                if (distance < 100) {
                    // Buat badge
                    const badge = document.createElement('div');
                    badge.innerHTML = `📍 ${distance.toFixed(1)} km`;
                    badge.style.cssText = `
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        background: #28a745;
                        color: white;
                        padding: 5px 10px;
                        border-radius: 15px;
                        font-size: 12px;
                        z-index: 100;
                        font-weight: bold;
                    `;
                    
                    // Pastikan parent element memiliki position relative
                    element.style.position = 'relative';
                    
                    // Tambahkan badge
                    element.appendChild(badge);
                    
                    console.log(`Added badge to news ${index}`);
                }
            }
        });
        
        alert('Badge berhasil ditambahkan ke berita terdekat!');
    }
    
    // Jalankan test saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
        
        // Buat tombol test
        const button = document.createElement('button');
        button.innerHTML = '🌍 TEST GEOLOCATION';
        button.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        `;
        button.onclick = testGeolocation;
        document.body.appendChild(button);
        
        console.log('Test button added');
        
        // Auto-test setelah 2 detik
        setTimeout(function() {
            console.log('Auto-testing geolocation...');
            testGeolocation();
        }, 2000);
    });
</script>
@endsection