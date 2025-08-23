<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('arcade.privacy_policy') }}
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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">{{ __('legal.privacy_policy') }}</h1>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-8"><em>{{ __('legal.last_updated', ['date' => date('F j, Y')]) }}</em></p>

                    <p class="mb-6">{{ __('legal.privacy.intro') }}</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">{{ __('legal.privacy.section_1_title') }}</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li><strong>{{ __('legal.privacy.section_1_content.account_data') }}</strong></li>
                        <li><strong>{{ __('legal.privacy.section_1_content.gameplay_data') }}</strong></li>
                        <li><strong>{{ __('legal.privacy.section_1_content.device_data') }}</strong></li>
                        <li><strong>{{ __('legal.privacy.section_1_content.payment_data') }}</strong></li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">2. How We Use Information</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>To operate the arcade platform (token balances, markets, shop).</li>
                        <li>To process payments for token purchases.</li>
                        <li>To improve performance, security, and user experience.</li>
                        <li>To comply with legal and regulatory obligations.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">3. Sharing of Information</h2>
                    <p class="mb-4">We do not sell or rent your personal data. We only share limited information with:</p>
                    <ul class="list-disc pl-6 mb-6">
                        <li><strong>Payment processors</strong> (for transactions).</li>
                        <li><strong>Hosting providers</strong> (to run the service).</li>
                        <li><strong>Law enforcement</strong> if required by law.</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">4. Data Retention</h2>
                    <p class="mb-6">We keep your account and gameplay data while your account is active. You can request deletion at any time.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">5. Your Rights</h2>
                    <p class="mb-6">Depending on your country, you may have rights to access, correct, or delete your data. Contact us at <strong><a href="mailto:support@forecastarcade.com" class="text-blue-600 hover:text-blue-800">support@forecastarcade.com</a></strong> for requests.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">6. Children</h2>
                    <p class="mb-6">Our service is <strong>18+ only</strong>. We do not knowingly collect information from minors.</p>

                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-8 mb-4">7. Contact</h2>
                    <p class="mb-6">If you have questions, contact us at <strong><a href="mailto:support@forecastarcade.com" class="text-blue-600 hover:text-blue-800">support@forecastarcade.com</a></strong>.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
