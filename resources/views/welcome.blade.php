<x-layout>
    @auth
        <h1>Hello {{ auth()->user()->full_name }}</h1>
    @endauth

    @guest
        <h1>Hello Guest</h1>
    @endguest
</x-layout>
