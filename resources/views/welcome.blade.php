@extends('layouts.layout')
    @section('content')
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
@endsection