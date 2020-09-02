@extends('layouts.admin')

@section('page-header', 'Blogs')

@section('content')

    <div class="row">
        <div class="col">
            @include('admin.shared.errors')
        </div>
    </div>

    <div class="row mb-2">
        <div class="col text-right">
            <a href="{{route('blogs.upsert')}}" class="btn btn-success btn-sm">
                <i data-feather="feather" class="mr-2"></i>
                Create
            </a>
        </div>
    </div>

    <div class="row">
        @foreach($blogs as $blog)
            @php /** @var \App\Models\Blog $blog */ @endphp
            <div class="col-md-6 mb-5">
                <div class="card">
                    <div class="card-header" style="display: block">
                        <span>{{$blog->title}}</span>
                        <a class="btn btn-sm btn-warning float-right mr-2"
                           href="{{route('blogs.upsert', $blog->id)}}" title="edit"
                        >
                            <i data-feather="edit"></i>
                        </a>
                        <a class="btn btn-sm btn-info float-right mr-2" target="_blank"
                           href="{{route('blogs.show', $blog->id)}}" title="Preview">
                            <i data-feather="eye"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Tagline:</strong> {{$blog->tagline}}
                        </p>
                        <p>
                            <strong>Status:</strong>
                            @if($blog->status === 'published')
                                <span class="badge badge-success">PUBLISHED</span>
                            @elseif($blog->status === 'draft')
                                <span class="badge badge-warning">DRAFT</span>
                            @endif
                        </p>
                        <p>
                            <strong>Moderation Status:</strong>
                            @if($blog->moderation_status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($blog->moderation_status === 'rejected')
                                <span class="badge badge-warning">DRAFT</span>
                            @else
                                <span class="badge badge-warning">PENDING</span><em> - waiting for approval</em>
                            @endif
                        </p>
                        @if($blog->status === 'published')
                            <p>
                                <strong>URL: </strong>
                                <a target="_blank" href="{{$blog->getUrl()}}">
                                    {{$blog->getUrl()}}
                                </a>
                            </p>
                        @endif

                        @if($blog->user->isBlogModerator())
                            <form action="{{route('blogs.moderate', $blog->id)}}" method="post" class="pt-3 border-top">
                                @csrf
                                <input type="hidden" name="id" value="{{$blog->id}}">
                                <div class="form-group">
                                    <label for="moderation_notes"><strong>Moderation Notes</strong></label>
                                    <textarea
                                        class="form-control"
                                        name="moderation_notes"
                                        id="moderation_notes"
                                        cols="30" rows="3"
                                    >{{old('moderation_notes',$blog->moderation_notes ?? '')}}</textarea>
                                </div>
                                <div class="mt-3">
                                    @if($blog->moderation_status !== 'approved')
                                        <button class="btn btn-sm btn-success ml-3 float-right" type="submit"
                                                name="moderation_status"
                                                value="approved">
                                            <i class="mr-1" data-feather="check"></i>
                                            Approve
                                        </button>
                                    @endif

                                    @if($blog->moderation_status !== 'rejected')
                                        <button class="btn btn-sm btn-danger ml-3 float-right" type="submit"
                                                name="moderation_status"
                                                value="rejected">
                                            <i class="mr-1" data-feather="x"></i>
                                            Reject
                                        </button>
                                    @endif

                                    @if($blog->moderation_status !== 'pending')

                                        <button class="btn btn-sm btn-info ml-3 float-right" type="submit"
                                                name="moderation_status"
                                                value="pending">
                                            <i class="mr-1" data-feather="skip-back"></i>
                                            Mark as pending
                                        </button>
                                    @endif

                                </div>
                            </form>
                        @else
                            <p><strong>Moderation Notes: </strong> {{$blog->moderation_notes ?: 'N/A'}}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{ $blogs->links() }}
    </div>

@endsection

