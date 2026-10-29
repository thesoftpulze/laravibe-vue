<x-mail::message>
# Your email is working

This test email was sent from the administration settings of {{ config('app.name') }}. If you can read it, the application can send email.

<x-mail::button :url="route('administration.settings.mail.edit')">
Back to mail settings
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
