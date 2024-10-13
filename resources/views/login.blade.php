<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vuexy - Vuejs Admin Dashboard Template</title>

</head>

<body>
    {{-- TODO: style this piece of shit --}}
    <div class="">
        <form action="/login" method="POST">
            @csrf
            <input type="text" name="email" value="admin@admin.net">
            <input type="password" name="password" value="password">
            <input type="submit">
        </form>
    </div>

</body>

</html>
