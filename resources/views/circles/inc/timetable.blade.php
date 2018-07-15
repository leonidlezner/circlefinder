<div class="card mt-4">
    <h5 class="card-header">Time schedule</h5>
    
    <div class="card-body">
        <table class="table table-sm table-striped table-bordered time-schedule">
            <tr>
                <th>Time</th>
                <th>User</th>
                @foreach($timeTable->getDayList(true) as $day)
                <th>{{ $day }}</th>
                @endforeach
            </tr>
        @foreach($timeTable->getTimeList() as $time)
            @foreach($timeTable->memberships() as $membership)
                @if($timeTable->atTime($time))
                <tr>
                    @if($loop->first)
                    <td rowspan="{{ count($timeTable->memberships()) }}">{{ $time }}:00</td>
                    @endif

                    <td><small>{{ $membership->user->name }}</small></td>
                                            
                    @foreach($timeTable->getDayList() as $day)
                        @if(is_array($membership->timeSlot->$day) && in_array($time, $membership->timeSlot->$day))
                        <td class="time-ok">✅</td>
                        @else
                        <td>✖️</td>
                        @endif
                    @endforeach
                </tr>
                @endif
            @endforeach
        @endforeach
        </table>
    </div>
</div>