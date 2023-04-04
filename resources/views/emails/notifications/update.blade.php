@component('mail::message')
Xin chào **{!! $name !!}**, bạn vừa nhận được một thông báo mới !<br>
{{$message}}<br>
Tin nhắn tự động. Vui lòng không trả lời<br>
@endcomponent
