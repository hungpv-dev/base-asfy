@extends('layouts.app')
@section('title')
    Trang chủ
@endsection
@section('content')
    <h1 class="alert alert-success">Trang chủ</h1>
    <button id="buttonLogin">Login</button>
@endsection
@section('script')
    <script>
        $("#buttonLogin").addEventListener('click', function() {
            axios.post('http://localhost:8000/login')
                .then(res => console.log(res))
        });
    </script>
@endsection
