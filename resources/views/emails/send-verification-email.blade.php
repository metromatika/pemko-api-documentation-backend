<x-mail::message>
# Yth. {{ $userName }}

Terima kasih telah menggunakan API Documentation Diskominfo. Mohon verifikasi email anda dengan kode di bawah ini:

Kode verifikasi email anda adalah: **{{ $verificationCode }}**. Kode akan valid selama 10 menit.

Kami perlu memastikan bahwa email Anda benar dan tidak disalahgunakan oleh pihak yang tidak berkepentingan.

Salam Hormat Kami,<br>
{{ config('app.name') }}
</x-mail::message>
