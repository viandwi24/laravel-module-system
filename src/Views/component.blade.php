<table class="table table-hover table-bordered">
    <thead>
        <th>#</th>
        <th>Name</th>
        <th>Description</th>
        <th>Version</th>
        <th>Author</th>
        <th>State</th>
        <th class="text-center">Action</th>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach ($modules as $item)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $item->info->name }}</td>
                <td>{{ $item->info->description }}</td>
                <td>{{ $item->info->version }}</td>
                <td>{{ $item->info->author }}</td>
                <td>
                    <span class="badge badge-{{ ($item->state == 'active') ? 'success' : 'danger' }}">{{ $item->state }}</span>
                </td>
                <td class="text-center">
                    @if ($item->state == 'active')
                        <a href="{{ route('module.disable', [$item->name]) }}" class="btn btn-sm btn-danger">Disable</a>
                    @else
                        <a href="{{ route('module.enable', [$item->name]) }}" class="btn btn-sm btn-success">Activate</a>
                    @endif
                </td>
            </tr>            
        @endforeach
    </tbody>
</table>