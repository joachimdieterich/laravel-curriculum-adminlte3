<tr>
<!--    <td>
        <div class="icheck-primary">
            <input type="checkbox" value="" id="check2">
            <label for="check2"></label>
        </div>
    </td>-->
<!--    <td class="mailbox-star">
        <a href="{{ route('messages.show', $thread->id) }}">
            <i class="fas fa-star-o text-warning"></i>
        </a>
    </td>-->
    <td class="mailbox-name">
        <a class="link-muted"
           href="{{ route('messages.show', $thread->id) }}">{{ $thread->creator()->username }}</a>
    </td>
    <td class="mailbox-subject {{ ($thread->userUnreadMessagesCount(Auth::id()) > 0) ? 'text-bold' : '' }}">
        <a class="link-muted" 
           href="{{ route('messages.show', $thread->id) }}">
            {{ $thread->subject }}
        </a>
    </td>
    <!--<td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>-->
    <td class="mailbox-date">
        <a class="link-muted"
           href="{{ route('messages.show', $thread->id) }}">
            {{ $thread->latestMessage->created_at->diffForHumans() }}
        </a>
    </td>
    <td class="mailbox-participants">
        <a class="link-muted"
           href="{{ route('messages.show', $thread->id) }}">
            {{ $thread->participantsString(Auth::id(), ['username']) }}
        </a>
    </td>
    <td><button type="button" class="btn btn-default btn-sm pull-right"><i class="far fa-trash-alt"></i></button></td>
    
</tr>