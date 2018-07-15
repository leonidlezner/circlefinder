<div class="card mt-4">
    <h5 class="card-header">Members</h5>

    <div class="card-body">
        @if(count($item->memberships))        
            <table class="table table-striped table-bordered">
                <tr>
                    <th>&nbsp;</th>
                    <th>Name</th>
                    <th>Type (virtual/f2f)</th>
                    <th>Begin</th>
                    <th>Language</th>
                </tr>
                
                @foreach($item->memberships as $memb)
                <tr>
                    <td class="align-middle text-center">                  
                        <span class="avatar">{!! user_avatar($memb->user, 40, false, true) !!}</span>
                    </td>
                    <td class="align-middle">
                        {!! $memb->user->link() !!}
                        @if($memb->comment)
                            <div>{{ $memb->comment }}</div>
                        @endif
                        @if($user->moderator())
                        {!! Form::open(['route' => ['circles.remove', 'uuid' => $item->uuid, 'user_uuid' => $memb->user->uuid]]) !!}
                            {{ Form::hidden('_method', 'DELETE') }}
                            {{ Form::submit('Remove', ['class' => 'delete btn btn-sm btn-danger confirm mt-2']) }}
                        {!! Form::close() !!}
                        @endif
                    </td>
                    <td class="align-middle">{{ translate_type($memb->type) }}</td>
                    <td class="align-middle">{{ format_date($memb->begin) }}</td>
                    <td class="align-middle">{{ list_languages($memb->languages) }}</td>
                </tr>
                @endforeach

                @for($i = 0; $i < $item->limit - count($item->memberships); $i++)
                    <tr>
                        <td class="align-middle">&nbsp;</td>
                        <td class="align-middle">&nbsp;</td>
                        <td class="align-middle">&nbsp;</td>
                        <td class="align-middle">&nbsp;</td>
                        <td class="align-middle">&nbsp;</td>
                    </tr>
                @endfor
            </table>
        @else
            <p>Currenly there are no members in the circle</p>
        @endif
    </div>
</div>