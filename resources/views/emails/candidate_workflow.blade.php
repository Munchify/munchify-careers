@component('mail::message')
# Hello {{ $candidateName ?: 'Candidate' }},

{!! nl2br(e($bodyContent)) !!}

<br>
Best regards,<br>
**{{ config('app.name', 'Munchify Careers') }}**
@endcomponent
