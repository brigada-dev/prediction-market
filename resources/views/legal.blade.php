<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Terms of Service') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
                <!-- Arcade Banner -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-8">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Arcade Mode — Tokens have no monetary value and cannot be redeemed.
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="prose prose-lg dark:prose-invert max-w-none">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">Terms of Service – Forecast Arcade</h1>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-8"><em>Last updated: {{ date('F j, Y') }}</em></p>

                    <p class="mb-6">Welcome to Forecast Arcade ("we," "our," or "us"). By using our platform, you agree to these Terms. Please read them carefully.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">1. Nature of the Service</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>Forecast Arcade is an <strong>entertainment platform</strong>, similar to a physical arcade.</li>
                        <li>Our tokens are <strong>arcade credits</strong>, not money.</li>
                        <li>Tokens can be used to participate in prediction games and to purchase items in our shop.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">2. Tokens</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>Tokens are <strong>non-redeemable</strong> and <strong>non-transferable</strong>.</li>
                        <li>Tokens have <strong>no cash value</strong> and cannot be exchanged for money, crypto, gift cards, or any other cash-equivalent.</li>
                        <li>Tokens may be obtained by:
                            <ul class="list-disc pl-6 mt-2">
                                <li>Earning through gameplay or ads,</li>
                                <li>Buying token packs with credit card or cryptocurrency.</li>
                            </ul>
                        </li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">3. Shop Items</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>Items available in our shop (digital perks, cosmetics, merch) are <strong>for entertainment only</strong>.</li>
                        <li>We do not offer cash-equivalent rewards such as prepaid cards, gift cards, or crypto vouchers.</li>
                        <li>All purchases are <strong>final and non-refundable</strong>.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">4. No Gambling</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>This is <strong>not a gambling platform</strong>.</li>
                        <li>Our tokens function like arcade credits. You can use them to play games and collect prizes, but you cannot cash out.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">5. Eligibility</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>You must be <strong>18 years or older</strong> to use Forecast Arcade.</li>
                        <li>By using the platform, you confirm that you meet this requirement.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">6. Payments</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>Payments for tokens are processed securely by third-party providers (Stripe, Coinbase Commerce).</li>
                        <li>We do not store your credit card or wallet details.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">7. Account Suspension</h2>
                    <p class="mb-6">We reserve the right to suspend or terminate accounts that abuse the platform, attempt fraud, or violate these Terms.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">8. Liability</h2>
                    <p class="mb-6">The service is provided "as is." We do not guarantee uninterrupted access or error-free operation.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">9. Changes</h2>
                    <p class="mb-6">We may update these Terms or our Privacy Policy. If we do, we'll notify users by posting the new versions with updated dates.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">10. Contact</h2>
                    <p class="mb-6">For questions about these Terms, email us at <strong><a href="mailto:support@forecastarcade.com" class="text-blue-600 hover:text-blue-800">support@forecastarcade.com</a></strong>.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
