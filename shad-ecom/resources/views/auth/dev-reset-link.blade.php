<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            🔧 Development Mode: Password Reset Link
        </h2>
        <p class="text-sm text-gray-600">
            This page is only available in local development. In production, users receive reset links via email.
        </p>
    </div>

    @if($link)
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">
                        Latest password reset link found!
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 rounded-lg p-4 mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Reset Link:</label>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    value="{{ $link }}"
                    readonly
                    id="reset-link"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 font-mono"
                >
                <button
                    onclick="copyLink()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium"
                >
                    Copy
                </button>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ $link }}" class="flex-1 text-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                Use This Link to Reset Password
            </a>
            <a href="{{ route('password.request') }}" class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                Request New Link
            </a>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-sm font-medium text-blue-900 mb-2">📧 Email Configuration</h3>
            <p class="text-sm text-blue-700 mb-2">
                Currently using: <span class="font-mono bg-blue-100 px-2 py-1 rounded">{{ config('mail.default') }}</span>
            </p>
            <p class="text-xs text-blue-600">
                To send actual emails, update your <code class="bg-blue-100 px-1 rounded">.env</code> file with SMTP settings (Gmail, Mailtrap, etc.)
            </p>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        {{ $message }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('password.request') }}" class="block text-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Go to Forgot Password Page
            </a>
        </div>
    @endif

    <script>
        function copyLink() {
            const input = document.getElementById('reset-link');
            input.select();
            document.execCommand('copy');

            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600');

            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }
    </script>
</x-guest-layout>
