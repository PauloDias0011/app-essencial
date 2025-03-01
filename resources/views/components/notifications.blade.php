<?
<div>
    @foreach (auth()->user()->notifications as $notification)
        <div class="bg-yellow-100 p-3 rounded mb-2">
            <strong>{{ $notification->data['title'] }}</strong>
            <p>{{ $notification->data['content'] }}</p>
        </div>
    @endforeach
</div>
