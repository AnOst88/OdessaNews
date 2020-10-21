@extends('layout.layout')
@section('content')
    <table class="table  text-center" id="data-table">
        <thead>
            <tr>
                <th class="text-center sort bg-secondary" style="cursor: pointer">ID</th>
                <th class="text-center sort bg-secondary" style="cursor: pointer">Title</th>
                <th class="text-center sort bg-secondary" style="cursor: pointer">Tags</th>
                <th class="text-center sort bg-secondary" style="cursor: pointer">
                    <a href="{{ route('news.sort', ['param' => 'authors', 'dir' => request()->input('dir') == 'desc' ? 'asc' : 'desc']) }}"
                        @if (request()->input('dir') == 'asc' && request()->input('param') == 'authors')
                        class="glyphicon glyphicon-chevron-down"
                    @else class="glyphicon glyphicon-chevron-up"
                        @endif>
                    </a>Authors
                </th>
                <th class="text-center sort bg-secondary" style="cursor: pointer"><a
                        href="{{ route('news.sort', ['param' => 'date', 'dir' => request()->input('dir') == 'desc' ? 'asc' : 'desc']) }}"
                        @if (request()->input('dir') == 'asc' && request()->input('param') == 'date')
                        class="glyphicon glyphicon-chevron-down"
                        @else class="glyphicon glyphicon-chevron-up"
                        @endif>
                    </a>Date
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($news as $new)
                <tr>
                    <td style="vertical-align: middle" class='bg-warning'>{{ $new->id }}</td>
                    <td style="vertical-align: middle" class='bg-dark'><a href="{{ $new->link }}">{{ $new->title }}</a>
                    </td>
                    <td style="vertical-align: middle" class='bg-warning'>{{ $new->tags }}</td>
                    <td style="vertical-align: middle" class='bg-primary'>{{ $new->authors }}</td>
                    <td style="vertical-align: middle" class='bg-primary'>{{ $new->date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-center" data-pagination>
        {{ $news->links() }}
    </div>
@endsection
