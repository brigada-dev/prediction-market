<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('arcade.purchase_tokens') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Arcade Banner -->
            <x-arcade-banner />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        🎮 {{ __('arcade.buy_arcade_tokens') }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{ __('arcade.purchase_description') }}
                    </p>
                </div>

                <!-- Current Balance -->
                @auth
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-8 text-center">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">{{ __('arcade.current_balance') }}</h3>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            🎮 {{ number_format(auth()->user()->balance, 0) }} {{ __('arcade.tokens') }}
                        </div>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">{{ __('arcade.no_monetary_value') }}</p>
                    </div>
                @endauth

                <!-- Token Packages -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <!-- Starter Pack -->
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('arcade.starter_pack') }}</h3>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">1,000 {{ __('arcade.tokens') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 mb-4">$10 USD</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('arcade.perfect_for_trying') }}</p>
                        <button onclick="purchaseTokens(1000, 'starter')" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            {{ __('arcade.buy_now') }}
                        </button>
                    </div>

                    <!-- Popular Pack -->
                    <div class="border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 text-center relative">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ __('arcade.popular') }}
                        </div>
                        <div class="text-4xl mb-4">🚀</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('arcade.gamer_pack') }}</h3>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">5,000 {{ __('arcade.tokens') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 mb-2">$45 USD</div>
                        <div class="text-green-600 text-sm mb-4">{{ __('arcade.save_amount', ['amount' => '$5']) }}</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('arcade.great_for_regular') }}</p>
                        <button onclick="purchaseTokens(5000, 'gamer')" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            {{ __('arcade.buy_now') }}
                        </button>
                    </div>

                    <!-- Pro Pack -->
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                        <div class="text-4xl mb-4">👑</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('arcade.pro_pack') }}</h3>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">10,000 {{ __('arcade.tokens') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 mb-2">$80 USD</div>
                        <div class="text-green-600 text-sm mb-4">{{ __('arcade.save_amount', ['amount' => '$20']) }}</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('arcade.maximum_value') }}</p>
                        <button onclick="purchaseTokens(10000, 'pro')" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            {{ __('arcade.buy_now') }}
                        </button>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 text-center">{{ __('arcade.payment_methods') }}</h3>
                    <div class="flex justify-center space-x-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">💳</span>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('arcade.credit_card') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('arcade.via_stripe') }}</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">₿</span>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('arcade.cryptocurrency') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('arcade.via_coinbase') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ __('arcade.important_notice') }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>{{ __('arcade.purchase_warnings.entertainment_only') }}</li>
                                    <li>{{ __('arcade.purchase_warnings.no_exchange') }}</li>
                                    <li>{{ __('arcade.purchase_warnings.final_purchase') }}</li>
                                    <li>{{ __('arcade.purchase_warnings.age_requirement') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal (placeholder) -->
    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 m-4 max-w-md w-full">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('arcade.choose_payment_method') }}</h3>
            <div class="space-y-3">
                <button id="stripeBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                    <span class="mr-2">💳</span>
                    {{ __('arcade.pay_with_credit_card') }}
                </button>
                <button id="coinbaseBtn" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                    <span class="mr-2">₿</span>
                    {{ __('arcade.pay_with_crypto') }}
                </button>
            </div>
            <button onclick="closePaymentModal()" class="w-full mt-4 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                {{ __('arcade.cancel') }}
            </button>
        </div>
    </div>

    <script>
        let selectedTokenAmount = 0;
        let selectedPackage = '';

        function purchaseTokens(amount, package) {
            selectedTokenAmount = amount;
            selectedPackage = package;
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('paymentModal').classList.add('flex');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentModal').classList.remove('flex');
        }

        document.getElementById('stripeBtn').addEventListener('click', function() {
            initiatePayment('stripe');
        });

        document.getElementById('coinbaseBtn').addEventListener('click', function() {
            initiatePayment('coinbase');
        });

        function initiatePayment(method) {
            // This is a placeholder implementation
            // In production, you would integrate with actual payment processors
            
            fetch('/purchase/tokens/intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    token_amount: selectedTokenAmount,
                    payment_method: method
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simulate successful payment for demo
                    setTimeout(() => {
                        completePurchase(data.payment_id, data.payment_provider);
                    }, 2000);
                    
                    closePaymentModal();
                    alert(`Processing ${selectedTokenAmount} token purchase via ${method}... This is a demo - tokens will be added shortly.`);
                } else {
                    alert('Error: ' + (data.error || 'Payment failed'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Payment failed. Please try again.');
            });
        }

        function completePurchase(paymentId, provider) {
            fetch('/purchase/tokens/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_id: paymentId,
                    payment_provider: provider
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Success! ${data.tokens_purchased} tokens added to your account. New balance: ${data.new_balance} tokens.`);
                    location.reload(); // Refresh to show new balance
                } else {
                    alert('Error completing purchase: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error completing purchase. Please contact support.');
            });
        }
    </script>
</x-app-layout>
