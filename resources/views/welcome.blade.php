<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @csrf
    
    @guest
        <p>Hello Guest</p>    
    @endguest

    @role('super_admin')
        <p>Hello Super Admin</p>
        <form method="GET" action="{{ route('logout.store') }}" >
            <li>
                <a href="/logout">
                    Logout
                </a>
            </li>
        </form>
    @endrole

    @role('admin')
        <p>Hello Admmin</p>
        <form method="GET" action="{{ route('logout.store') }}" >
            <li>
                <a href="/logout">
                    Logout
                </a>
            </li>
        </form>
    @endrole

    @role('member')
        <p>Hello Member</p>
        <form method="GET" action="{{ route('logout.store') }}" >
            <li>
                <a href="/logout">
                    Logout
                </a>
            </li>
        </form>
    @endrole
</body>
</html>