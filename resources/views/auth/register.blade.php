<x-layout>
    <h1>Register</h1>
    <div>
        <div>Register a new account</div>
        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div>
                <label for="full_name">Name</label>
                <input type="text" name="full_name" placeholder="Name" value="{{ old('full_name') }}">
                @error('full_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm Password">
                @error('password_confirmation')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</x-layout>
