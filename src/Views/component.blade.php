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
                    @switch($item->state)
                        @case('ready')
                            <div class="badge badge-success">Active</div>
                            @break

                        @case('not_ready')
                            <div class="badge badge-success">Active</div>
                            <div class="badge badge-warning">Must be Setup</div>
                            @break

                        @case('error')
                            <div class="badge badge-danger">Error</div>
                            @break    
                            
                        @default
                            <div class="badge badge-danger">Disable</div>                        
                    @endswitch
                </td>
                <td class="text-center">
                    @if ($item->state == 'ready' || $item->state == 'not_ready')
                        @if ($item->state == 'not_ready')
                            <a href="{{ @$item->setup }}" class="link text-primary">Setup</a> |
                        @endif
                        <a href="{{ route('module.disable', [$item->name]) }}" class="link text-danger">Disable</a>
                    @else
                        <a href="{{ route('module.enable', [$item->name]) }}" class="link text-success">Activate</a>
                    @endif
                </td>
            </tr>            
        @endforeach
    </tbody>
</table>