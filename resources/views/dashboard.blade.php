@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Dashboard</h3>
                    <h6 class="op-7 mb-2">Welcome, <span class="fw-bold">{{ auth()->user()->name }}</span></h6>
                </div>
            </div>
            @if (auth()->user()->hasRole('Super Admin'))
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Total Users</p>
                                            <h4 class="card-title">{{ $totalUsers }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                                            <i class="far fa-newspaper"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Total News</p>
                                            <h4 class="card-title">{{ $totalNews }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="far fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Publish News</p>
                                            <h4 class="card-title">{{ $totalNewsAccepted }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                                            <i class="fas fa-spinner"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Pending News</p>
                                            <h4 class="card-title">{{ $totalNewsNotAccepted }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Multiple Bar Chart</div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="multipleBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Pie Chart</div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="pieChart" style="width: 50%; height: 50%"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->user()->hasRole('Writer'))
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-stats card-success card-round">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="icon-big text-center">
                                            <i class="far fa-check-circle fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-stats">
                                                                <div class="numbers">
                            <p class="card-category">Publish News</p>
                            <h4 class="card-title">
                                {{ auth()->user()->news()->where('status', 'Published')->count() }}
                            </h4>
                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-warning card-round">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-spinner fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Pending News</p>
                                            <h4 class="card-title">
                                                {{ auth()->user()->news()->where('status', 'Pending')->count() }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-danger card-round">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-times-circle fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Reject News</p>
                                            <h4 class="card-title">
                                                {{ auth()->user()->news()->where('status', 'Reject')->count() }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->user()->hasRole('Editor'))
                <div class="row">
                    <div class="col-6">
                        <div class="card card-stats card-primary card-round">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="icon-big text-center">
                                            <i class="far fa-newspaper fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Total News</p>
                                            <h4 class="card-title">
                                                {{ $totalNews }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card card-stats card-warning card-round">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-spinner fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Pending News</p>
                                            <h4 class="card-title">
                                                {{ $totalNewsNotAccepted }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabel Management Berita untuk Super Admin dan Editor -->
            @if (auth()->user()->hasRole(['Admin', 'Editor']))
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Recent News Management</h4>
                                    <a href="{{ route('admin.news.manage') }}" class="btn btn-primary btn-sm ms-auto">
                                        <span class="btn-label">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        View All News
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th>Views</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentNews as $news)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($news->image)
                                                                <img src="{{ asset('storage/images/' . $news->image) }}" 
                                                                     alt="News Image" 
                                                                     class="rounded me-2" 
                                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                            @endif
                                                            <div>
                                                                <strong>{{ Str::limit($news->title, 50) }}</strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $news->author->name }}</td>
                                                    <td>{{ $news->category->name }}</td>
                                                    <td>
                                                        <span class="badge 
                                                            @if($news->status === 'Published') badge-success
                                                            @elseif($news->status === 'Pending') badge-warning
                                                            @else badge-secondary
                                                            @endif">
                                                            {{ $news->status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($news->views) }}</td>
                                                    <td>{{ $news->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            @if(auth()->user()->hasRole('Super Admin'))
                                                                <a href="{{ route('admin.news.edit', $news->id) }}" 
                                                                   class="btn btn-link btn-primary btn-lg" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="Edit News">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            @endif
                                                            
                                                            <a href="{{ route('news.show', $news->id) }}" 
                                                               class="btn btn-link btn-info btn-lg" 
                                                               data-bs-toggle="tooltip" 
                                                               title="View News">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            
                                                            @if(auth()->user()->hasRole('Super Admin'))
                                                                <button type="button" 
                                                                        class="btn btn-link btn-danger" 
                                                                        data-bs-toggle="tooltip" 
                                                                        title="Delete News"
                                                                        onclick="deleteNews({{ $news->id }})">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No news found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('custom-footer')
    <script>
        var usersPerMonth = @json($usersPerMonth);
        var newsPerMonth = @json($newsPerMonth);
        var totalUsersCurrentMonth = @json($totalUsersCurrentMonth);
        var totalNewsCurrentMonth = @json($totalNewsCurrentMonth);
        var currentMonth = @json($currentMonth);
        
        function deleteNews(newsId) {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this news!",
                type: "warning",
                buttons: {
                    confirm: {
                        text: "Yes, delete it!",
                        className: "btn btn-success",
                    },
                    cancel: {
                        visible: true,
                        className: "btn btn-danger",
                    },
                },
            }).then((Delete) => {
                if (Delete) {
                    $.ajax({
                        url: `/admin/news/${newsId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                swal("Deleted!", response.message, "success").then(() => {
                                    location.reload();
                                });
                            } else {
                                swal("Error!", response.message, "error");
                            }
                        },
                        error: function() {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                } else {
                    swal("Cancelled", "Your news is safe :)", "error");
                }
            });
        }
    </script>
    <script src="{{ asset('js/charts.js') }}"></script>
@endsection
