<x-app-layout>
	<!-- Polymarket-style hero section -->
	<section class="bg-slate-900 border-b border-slate-800">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
			@php $mm = app(\App\Services\MarketMaker::class); $stats = $mm->getMarketStats($market); @endphp
			
			<!-- Breadcrumb -->
			<div class="mb-4">
				<a href="{{ route('markets.index') }}" class="text-slate-400 hover:text-white text-sm flex items-center gap-2">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
					</svg>
					Markets
				</a>
			</div>

			<div class="flex items-start gap-6">
				<!-- Market title and info -->
				<div class="flex-1 min-w-0">
					<div class="flex items-center gap-3 mb-3">
						@if($market->choices()->exists())
							<div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
								<svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
									<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
								</svg>
							</div>
						@else
							<div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
								<span class="text-white font-bold text-lg">?</span>
							</div>
						@endif
						
						<div>
							@if($market->resolved)
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
									Resolved
								</span>
							@elseif($market->isClosed())
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
									Closed
								</span>
							@else
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
									Active
								</span>
							@endif
						</div>
					</div>
					
					<h1 class="text-2xl lg:text-3xl font-bold text-white mb-3 leading-tight">{{ $market->title }}</h1>
					
					@if($market->description)
						<p class="text-slate-300 text-base mb-4 max-w-4xl">{{ $market->description }}</p>
					@endif

					<div class="flex items-center gap-6 text-sm text-slate-400">
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
							<span>Ends {{ $market->closes_at->format('M j, Y \a\t g:i A') }}</span>
						</div>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
							</svg>
							<span>${{ number_format($stats['total_volume'], 0) }} Vol.</span>
						</div>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
							</svg>
							<span>{{ $stats['total_positions'] }} Positions</span>
						</div>
					</div>
				</div>

				<!-- Current leader/stats -->
					@if($market->choices()->exists())
						@php $choiceProbs = $stats['choice_probabilities'] ?? []; @endphp
					<div class="hidden lg:block bg-slate-800 rounded-xl p-4 min-w-[200px]">
						<div class="text-xs text-slate-400 mb-1">Current leader</div>
						<div class="text-white font-semibold text-lg">{{ $stats['top_choice_name'] ?? '—' }}</div>
						<div class="text-slate-300">{{ $stats['top_probability'] ?? 0 }}% chance</div>
						</div>
					@else
					<div class="hidden lg:block bg-slate-800 rounded-xl p-4 min-w-[200px]">
						<div class="text-xs text-slate-400 mb-2">Current odds</div>
						<div class="space-y-1">
							<div class="flex justify-between text-sm">
								<span class="text-slate-300">Yes</span>
								<span class="text-green-400 font-medium">{{ $stats['probability_yes'] ?? 0 }}%</span>
							</div>
							<div class="flex justify-between text-sm">
								<span class="text-slate-300">No</span>
								<span class="text-red-400 font-medium">{{ $stats['probability_no'] ?? 0 }}%</span>
							</div>
						</div>
						</div>
					@endif
			</div>
		</div>
	</section>

	<div class="py-8">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="grid lg:grid-cols-3 gap-8">
				<!-- Main -->
				<div class="lg:col-span-2 space-y-6">
					<!-- Chart and timeframe -->
					@if($market->choices()->exists())
						@php $prices = $mm->price($market); @endphp
						<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
							<div class="p-6 pb-4">
								<div class="flex items-center justify-between mb-6">
									<h3 class="text-lg font-semibold text-slate-900 dark:text-white">Market Activity</h3>
									<div class="flex items-center bg-slate-100 dark:bg-slate-700 rounded-lg p-1" id="timeRangeButtons">
										<button onclick="updateChart(168)" class="time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="168">ALL</button>
										<button onclick="updateChart(720)" class="time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="720">1M</button>
										<button onclick="updateChart(168)" class="time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="168">1W</button>
										<button onclick="updateChart(24)" class="time-btn px-3 py-1.5 text-xs font-medium rounded-md bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm" data-hours="24">1D</button>
										<button onclick="updateChart(6)" class="time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="6">6H</button>
										<button onclick="updateChart(1)" class="time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="1">1H</button>
									</div>
								</div>
								<div class="h-80">
									<canvas id="probChart"></canvas>
								</div>
							</div>
							<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
							<script>
								(function(){
									const init = () => {
										const canvas = document.getElementById('probChart');
										if (!canvas) return;
										
										const labels = @json($market->choices->pluck('name'));
										const slugs = @json($market->choices->pluck('slug'));
										const historicalData = @json($historicalData);
										const isDark = document.documentElement.classList.contains('dark');
										
										// Process historical data
										const timestamps = historicalData.timestamps || [];
										const priceHistory = historicalData.prices || [];
										
										// Create time labels from actual timestamps
										const timeLabels = timestamps.map(timestamp => {
											const date = new Date(timestamp);
											return date.toLocaleTimeString('en-US', { 
												hour: '2-digit', 
												minute: '2-digit',
												month: 'short',
												day: 'numeric'
											});
										});
										
										const colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
										
										// Create datasets from real historical data
										const datasets = labels.map((label, index) => {
											const slug = slugs[index];
											
											// Extract price data for this choice over time
											const data = priceHistory.map(pricePoint => {
												const price = (pricePoint[slug] || 0) * 100;
												return Math.round(price * 10) / 10;
											});
											
											return {
												label: label,
												data: data,
												borderColor: colors[index % colors.length],
												backgroundColor: colors[index % colors.length] + '20',
												borderWidth: 2,
												fill: false,
												tension: 0.1,
												pointRadius: 1,
												pointHoverRadius: 4,
											};
										});

										const ctx = canvas.getContext('2d');
										chartInstance = new Chart(ctx, {
											type: 'line',
											data: {
												labels: timeLabels,
												datasets: datasets
											},
											options: {
												responsive: true,
												maintainAspectRatio: false,
												interaction: {
													intersect: false,
													mode: 'index'
												},
												scales: {
													x: {
														display: true,
														grid: {
															color: isDark ? '#374151' : '#F3F4F6',
															drawBorder: false
														},
														ticks: {
															color: isDark ? '#9CA3AF' : '#6B7280',
															maxTicksLimit: 8,
															callback: function(value, index) {
																// Show fewer labels on mobile
																if (window.innerWidth < 768 && index % 2 !== 0) {
																	return '';
																}
																return this.getLabelForValue(value);
															}
														}
													},
													y: {
														display: true,
														min: 0,
														max: 100,
														grid: {
															color: isDark ? '#374151' : '#F3F4F6',
															drawBorder: false
														},
														ticks: {
															color: isDark ? '#9CA3AF' : '#6B7280',
															callback: function(value) {
																return value + '%';
															}
														}
													}
												},
												plugins: {
													legend: {
														display: true,
														position: 'bottom',
														labels: {
															color: isDark ? '#E5E7EB' : '#374151',
															usePointStyle: true,
															padding: 20,
															font: {
																size: 12
															}
														}
													},
													tooltip: {
														backgroundColor: isDark ? '#1F2937' : '#FFFFFF',
														titleColor: isDark ? '#E5E7EB' : '#374151',
														bodyColor: isDark ? '#E5E7EB' : '#374151',
														borderColor: isDark ? '#374151' : '#E5E7EB',
														borderWidth: 1,
														cornerRadius: 8,
														callbacks: {
															title: function(context) {
																const timestamp = timestamps[context[0].dataIndex];
																const date = new Date(timestamp);
																return date.toLocaleDateString('en-US', {
																	month: 'short',
																	day: 'numeric',
																	hour: '2-digit',
																	minute: '2-digit'
																});
															},
															label: function(context) {
																return context.dataset.label + ': ' + context.parsed.y + '%';
															}
														}
													}
												}
											}
										});
									};
									
									if (document.readyState === 'loading') {
										document.addEventListener('DOMContentLoaded', init);
									} else {
										init();
									}
								})();
							</script>

							<!-- Outcomes table with Polymarket design -->
							<div class="border-t border-slate-200 dark:border-slate-700">
								<div class="p-6">
									<div class="space-y-3">
								@foreach($market->choices as $i => $choice)
									@php 
										$slug = $choice->slug; 
										$prob = isset($prices[$slug]) ? round($prices[$slug]*100,1) : 0; 
										$pricePerShare = $mm->costToBuy($market, $slug, 1);
										$yesCents = max(1, (int) round($pricePerShare * 100));
										$noCents = max(1, 100 - $yesCents);
												$colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
												$color = $colors[$i % count($colors)];
												$volume = number_format(rand(10000, 500000), 0);
									@endphp
																				<div class="outcome-card group bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-colors cursor-pointer"
										 data-choice="{{ $slug }}"
										 onclick="selectChoice('{{ $slug }}', '{{ $choice->name }}', {{ $prob }}, {{ $yesCents }})">
										<div class="p-4">
											<div class="flex items-center justify-between">
												<div class="flex items-center gap-4 flex-1 min-w-0">
													<!-- Candidate info -->
													<div class="flex items-center gap-3 flex-1 min-w-0">
														<div class="relative">
															<div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center">
																<span class="text-slate-600 dark:text-slate-300 font-semibold text-lg">{{ substr($choice->name, 0, 1) }}</span>
															</div>
														</div>
														<div class="min-w-0 flex-1">
															<h4 class="font-semibold text-slate-900 dark:text-white truncate">{{ $choice->name }}</h4>
															<p class="text-sm text-slate-500 dark:text-slate-400 truncate">{{ $choice->party ?? 'Independent' }}</p>
															<div class="flex items-center gap-4 mt-1">
																<span class="text-xs text-slate-400">${{ $volume }} Vol.</span>
															</div>
														</div>
													</div>
													
													<!-- Probability -->
													<div class="text-right">
														<div class="text-2xl font-bold" style="color: {{ $color }}">{{ $prob }}%</div>
														<div class="text-xs text-slate-500 dark:text-slate-400">
															@if($prob > 50)
																<span class="text-green-600 dark:text-green-400">+{{ $prob - 50 }}%</span>
															@elseif($prob < 50)
																<span class="text-red-600 dark:text-red-400">{{ $prob - 50 }}%</span>
															@else
																<span class="text-slate-500">Even</span>
															@endif
														</div>
													</div>
												</div>
												
												<!-- Buy buttons -->
												<div class="flex items-center gap-2 ml-6">
													<button onclick="event.stopPropagation(); selectChoiceAndFocus('{{ $slug }}', '{{ $choice->name }}', {{ $prob }}, {{ $yesCents }})" 
															class="px-4 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white transition-colors min-w-[80px]">
														Buy {{ $yesCents }}¢
													</button>
													<button type="button" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600/20 text-red-600 dark:text-red-400 cursor-not-allowed transition-colors min-w-[80px]" title="Coming soon">
														Sell {{ $noCents }}¢
													</button>
												</div>
											</div>
													
													<!-- Progress bar -->
													<div class="mt-4">
														<div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
															<span>Probability</span>
															<span>{{ $prob }}%</span>
										</div>
														<div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
															<div class="h-2 rounded-full transition-all duration-500" style="width: {{ $prob }}%; background-color: {{ $color }}"></div>
										</div>
										</div>
										</div>
									</div>
								@endforeach
									</div>
								</div>
							</div>
						</div>
					@else
						<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
							<div class="p-6 pb-4">
								<div class="flex items-center justify-between mb-6">
									<h3 class="text-lg font-semibold text-slate-900 dark:text-white">Market Activity</h3>
									<div class="flex items-center bg-slate-100 dark:bg-slate-700 rounded-lg p-1" id="binaryTimeRangeButtons">
										<button onclick="updateBinaryChart(168)" class="binary-time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="168">ALL</button>
										<button onclick="updateBinaryChart(720)" class="binary-time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="720">1M</button>
										<button onclick="updateBinaryChart(168)" class="binary-time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="168">1W</button>
										<button onclick="updateBinaryChart(24)" class="binary-time-btn px-3 py-1.5 text-xs font-medium rounded-md bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm" data-hours="24">1D</button>
										<button onclick="updateBinaryChart(6)" class="binary-time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="6">6H</button>
										<button onclick="updateBinaryChart(1)" class="binary-time-btn px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white" data-hours="1">1H</button>
									</div>
								</div>
								<div class="h-80">
									<canvas id="binaryChart"></canvas>
								</div>
							</div>
							<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
							<script>
								(function(){
									const init = () => {
										const canvas = document.getElementById('binaryChart');
										if (!canvas) return;
										
										const historicalData = @json($historicalData);
										const isDark = document.documentElement.classList.contains('dark');
										
										// Process historical data for binary market
										const timestamps = historicalData.timestamps || [];
										const priceHistory = historicalData.prices || [];
										
										// Create time labels from actual timestamps
										const timeLabels = timestamps.map(timestamp => {
											const date = new Date(timestamp);
											return date.toLocaleTimeString('en-US', { 
												hour: '2-digit', 
												minute: '2-digit',
												month: 'short',
												day: 'numeric'
											});
										});
										
										// Create datasets for Yes/No
										const yesData = priceHistory.map(pricePoint => {
											const price = (pricePoint.yes || 0.5) * 100;
											return Math.round(price * 10) / 10;
										});
										
										const noData = priceHistory.map(pricePoint => {
											const price = (pricePoint.no || 0.5) * 100;
											return Math.round(price * 10) / 10;
										});
										
										const datasets = [
											{
												label: 'Yes',
												data: yesData,
												borderColor: '#10B981',
												backgroundColor: '#10B98120',
												borderWidth: 2,
												fill: false,
												tension: 0.1,
												pointRadius: 1,
												pointHoverRadius: 4,
											},
											{
												label: 'No',
												data: noData,
												borderColor: '#EF4444',
												backgroundColor: '#EF444420',
												borderWidth: 2,
												fill: false,
												tension: 0.1,
												pointRadius: 1,
												pointHoverRadius: 4,
											}
										];

										const ctx = canvas.getContext('2d');
										window.binaryChartInstance = new Chart(ctx, {
											type: 'line',
											data: {
												labels: timeLabels,
												datasets: datasets
											},
											options: {
												responsive: true,
												maintainAspectRatio: false,
												interaction: {
													intersect: false,
													mode: 'index'
												},
												scales: {
													x: {
														display: true,
														grid: {
															color: isDark ? '#374151' : '#F3F4F6',
															drawBorder: false
														},
														ticks: {
															color: isDark ? '#9CA3AF' : '#6B7280',
															maxTicksLimit: 8
														}
													},
													y: {
														display: true,
														min: 0,
														max: 100,
														grid: {
															color: isDark ? '#374151' : '#F3F4F6',
															drawBorder: false
														},
														ticks: {
															color: isDark ? '#9CA3AF' : '#6B7280',
															callback: function(value) {
																return value + '%';
															}
														}
													}
												},
												plugins: {
													legend: {
														display: true,
														position: 'bottom',
														labels: {
															color: isDark ? '#E5E7EB' : '#374151',
															usePointStyle: true,
															padding: 20,
															font: {
																size: 12
															}
														}
													},
													tooltip: {
														backgroundColor: isDark ? '#1F2937' : '#FFFFFF',
														titleColor: isDark ? '#E5E7EB' : '#374151',
														bodyColor: isDark ? '#E5E7EB' : '#374151',
														borderColor: isDark ? '#374151' : '#E5E7EB',
														borderWidth: 1,
														cornerRadius: 8,
														callbacks: {
															title: function(context) {
																const timestamp = timestamps[context[0].dataIndex];
																const date = new Date(timestamp);
																return date.toLocaleDateString('en-US', {
																	month: 'short',
																	day: 'numeric',
																	hour: '2-digit',
																	minute: '2-digit'
																});
															},
															label: function(context) {
																return context.dataset.label + ': ' + context.parsed.y + '%';
															}
														}
													}
												}
											}
										});
									};
									
									if (document.readyState === 'loading') {
										document.addEventListener('DOMContentLoaded', init);
									} else {
										init();
									}
								})();
							</script>
						</div>
							
							<!-- Binary market trading section -->
							<div class="border-t border-slate-200 dark:border-slate-700 p-6">
								<div class="grid grid-cols-2 gap-4">
									<div class="outcome-card bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 cursor-pointer transition-all hover:border-green-300 dark:hover:border-green-700" 
										 data-choice="yes" onclick="selectBinaryChoice('yes')">
										<div class="text-center">
											<div class="text-green-600 dark:text-green-400 font-bold text-2xl mb-2">{{ $stats['probability_yes'] }}%</div>
											<div class="text-slate-600 dark:text-slate-400 text-sm mb-4">Chance of Yes</div>
											<button onclick="event.stopPropagation(); selectBinaryChoice('yes')" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
												Buy Yes
											</button>
										</div>
									</div>
									<div class="outcome-card bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 cursor-pointer transition-all hover:border-red-300 dark:hover:border-red-700" 
										 data-choice="no" onclick="selectBinaryChoice('no')">
										<div class="text-center">
											<div class="text-red-600 dark:text-red-400 font-bold text-2xl mb-2">{{ $stats['probability_no'] }}%</div>
											<div class="text-slate-600 dark:text-slate-400 text-sm mb-4">Chance of No</div>
											<button onclick="event.stopPropagation(); selectBinaryChoice('no')" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
												Buy No
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endif

					<!-- User positions -->
					@auth
						@if($userPositions->count() > 0)
							<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
								<div class="p-6">
									<div class="flex items-center gap-2 mb-6">
										<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
										</svg>
										<h3 class="text-lg font-semibold text-slate-900 dark:text-white">Your Positions</h3>
									</div>
									
								<div class="space-y-3">
									@foreach($userPositions as $position)
											<div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg border border-slate-200 dark:border-slate-600">
												<div class="flex items-center gap-3">
													<div class="w-8 h-8 rounded-lg {{ $position->choice === 'yes' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} flex items-center justify-center">
														@if($position->choice === 'yes')
															<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
															</svg>
														@else
															<svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
															</svg>
														@endif
													</div>
											<div>
														<div class="font-semibold {{ $position->choice === 'yes' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
															{{ strtoupper($position->choice) }}
														</div>
														<div class="text-sm text-slate-600 dark:text-slate-400">{{ number_format($position->shares, 2) }} shares</div>
													</div>
											</div>
											<div class="text-right">
													<div class="font-medium text-slate-900 dark:text-white">${{ number_format($position->cost, 2) }}</div>
													<div class="text-xs text-slate-500 dark:text-slate-400">{{ $position->created_at->format('M j, g:i A') }}</div>
											</div>
										</div>
									@endforeach
								</div>
									
								@php
									$totalYesShares = $userPositions->where('choice', 'yes')->sum('shares');
									$totalNoShares = $userPositions->where('choice', 'no')->sum('shares');
									$totalCost = $userPositions->sum('cost');
								@endphp
									<div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
										<div class="grid grid-cols-3 gap-4 text-sm">
											<div class="text-center">
												<div class="text-slate-600 dark:text-slate-400 mb-1">Yes Shares</div>
												<div class="font-semibold text-green-600 dark:text-green-400">{{ number_format($totalYesShares, 1) }}</div>
											</div>
											<div class="text-center">
												<div class="text-slate-600 dark:text-slate-400 mb-1">No Shares</div>
												<div class="font-semibold text-red-600 dark:text-red-400">{{ number_format($totalNoShares, 1) }}</div>
											</div>
											<div class="text-center">
												<div class="text-slate-600 dark:text-slate-400 mb-1">Total Invested</div>
												<div class="font-semibold text-slate-900 dark:text-white">${{ number_format($totalCost, 2) }}</div>
									</div>
									</div>
									</div>
								</div>
							</div>
						@endif
					@endauth
				</div>

				<!-- Right sidebar -->
				<div class="space-y-6 lg:sticky lg:top-6 h-fit" id="sidebar">
					<!-- Buy Modal -->
					@auth
						<div id="buy-modal-container">
							<x-buy-modal :market="$market" :user-balance="auth()->user()->balance" />
						</div>
					@else
						<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
							<div class="text-center">
								<svg class="w-12 h-12 mx-auto mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
								</svg>
								<h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Start Trading</h3>
								<p class="text-slate-600 dark:text-slate-400 mb-4">Login to trade on this market and earn rewards</p>
								<a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
									Login to Trade
								</a>
							</div>
						</div>
					@endauth

					<!-- Market Stats - Polymarket style -->
					<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
						<div class="p-6">
							<div class="flex items-center justify-between mb-6">
								<h3 class="text-lg font-semibold text-slate-900 dark:text-white">Market Info</h3>
								<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
								</svg>
							</div>

							<div class="space-y-4">
								<!-- Volume -->
								<div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
											<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
											</svg>
										</div>
										<div>
											<div class="text-sm text-slate-600 dark:text-slate-400">Volume</div>
											<div class="font-semibold text-slate-900 dark:text-white">${{ number_format($stats['total_volume'], 0) }}</div>
										</div>
									</div>
									<div class="text-right">
										<div class="text-xs text-green-600 dark:text-green-400">+12.3%</div>
										<div class="text-xs text-slate-500">24h</div>
									</div>
								</div>

								<!-- Liquidity -->
								<div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
											<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
											</svg>
							</div>
								<div>
											<div class="text-sm text-slate-600 dark:text-slate-400">Liquidity</div>
											<div class="font-semibold text-slate-900 dark:text-white">${{ number_format($stats['total_volume'] * 2.5, 0) }}</div>
										</div>
									</div>
									<div class="text-right">
										<div class="text-xs text-green-600 dark:text-green-400">Deep</div>
									</div>
								</div>

								<!-- Active Positions -->
								<div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
											<svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
											</svg>
								</div>
								<div>
											<div class="text-sm text-slate-600 dark:text-slate-400">Positions</div>
											<div class="font-semibold text-slate-900 dark:text-white">{{ $stats['total_positions'] }}</div>
										</div>
									</div>
									<div class="text-right">
										<div class="text-xs text-blue-600 dark:text-blue-400">{{ number_format(($stats['total_positions'] / 100) * 80, 0) }} Unique</div>
									</div>
								</div>
							</div>

							<!-- Probability Distribution -->
							@if($market->choices()->exists())
								@php $choiceProbs = $stats['choice_probabilities'] ?? []; @endphp
								<div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
									<h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Probability Distribution</h4>
									<div class="space-y-3">
										@foreach($market->choices->take(3) as $i => $choice)
											@php 
												$prob = $choiceProbs[$choice->slug] ?? 0;
												$colors = ['#3B82F6', '#EF4444', '#10B981'];
												$color = $colors[$i % count($colors)];
											@endphp
											<div>
												<div class="flex justify-between text-sm mb-1">
													<span class="text-slate-600 dark:text-slate-400 truncate">{{ $choice->name }}</span>
													<span class="font-medium text-slate-900 dark:text-white">{{ $prob }}%</span>
												</div>
												<div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
													<div class="h-2 rounded-full transition-all duration-300" style="width: {{ $prob }}%; background-color: {{ $color }}"></div>
												</div>
											</div>
										@endforeach
								</div>
							</div>
						@endif
						</div>
					</div>

					<!-- How it Works -->
					<div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
						<div class="flex items-center gap-2 mb-4">
							<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
							<h4 class="font-semibold text-slate-900 dark:text-white">How it works</h4>
						</div>
						<div class="text-sm text-slate-600 dark:text-slate-400 space-y-3">
							<div class="flex items-start gap-3">
								<div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center shrink-0 mt-0.5">
									<span class="text-green-600 dark:text-green-400 text-xs font-bold">1</span>
								</div>
								<p>Buy "Yes" shares if you think the outcome will happen</p>
							</div>
							<div class="flex items-start gap-3">
								<div class="w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center shrink-0 mt-0.5">
									<span class="text-red-600 dark:text-red-400 text-xs font-bold">2</span>
								</div>
								<p>Buy "No" shares if you think it won't happen</p>
							</div>
							<div class="flex items-start gap-3">
								<div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center shrink-0 mt-0.5">
									<span class="text-blue-600 dark:text-blue-400 text-xs font-bold">3</span>
								</div>
								<p>Prices change based on market demand</p>
							</div>
							<div class="flex items-start gap-3">
								<div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center shrink-0 mt-0.5">
									<span class="text-purple-600 dark:text-purple-400 text-xs font-bold">4</span>
								</div>
								<p>Winning shares pay out when the market resolves</p>
							</div>
						</div>
					</div>

					<!-- Market Resolution -->
					<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
						<div class="flex items-center gap-2 mb-4">
							<svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
							<h4 class="font-semibold text-slate-900 dark:text-white">Resolution</h4>
						</div>
						<div class="space-y-3 text-sm">
							<div class="flex justify-between">
								<span class="text-slate-600 dark:text-slate-400">Market ends</span>
								<span class="font-medium text-slate-900 dark:text-white">{{ $market->closes_at->format('M j, Y') }}</span>
							</div>
							<div class="flex justify-between">
								<span class="text-slate-600 dark:text-slate-400">Time left</span>
								<span class="font-medium text-slate-900 dark:text-white">{{ $market->closes_at->diffForHumans() }}</span>
							</div>
							<div class="flex justify-between">
								<span class="text-slate-600 dark:text-slate-400">Resolution source</span>
								<span class="font-medium text-slate-900 dark:text-white">Official results</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Global chart instance
		let chartInstance = null;
		const marketId = {{ $market->id }};

		// Function to update chart with different time ranges
		async function updateChart(hours) {
			try {
				// Update button states
				document.querySelectorAll('.time-btn').forEach(btn => {
					btn.classList.remove('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white', 'shadow-sm');
					btn.classList.add('text-slate-600', 'dark:text-slate-300');
				});
				
				// Highlight selected button
				const selectedBtn = document.querySelector(`[data-hours="${hours}"]`);
				if (selectedBtn) {
					selectedBtn.classList.remove('text-slate-600', 'dark:text-slate-300');
					selectedBtn.classList.add('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white', 'shadow-sm');
				}

				// Fetch new data
				const response = await fetch(`/api/markets/${marketId}/historical-prices?hours=${hours}`);
				const historicalData = await response.json();
				
				// Update chart
				if (chartInstance && historicalData.timestamps && historicalData.prices) {
					updateChartData(historicalData);
				}
			} catch (error) {
				console.error('Error updating chart:', error);
			}
		}

		// Function to update chart data
		function updateChartData(historicalData) {
			const timestamps = historicalData.timestamps || [];
			const priceHistory = historicalData.prices || [];
			const labels = @json($market->choices->pluck('name'));
			const slugs = @json($market->choices->pluck('slug'));
			
			// Create time labels from actual timestamps
			const timeLabels = timestamps.map(timestamp => {
				const date = new Date(timestamp);
				return date.toLocaleTimeString('en-US', { 
					hour: '2-digit', 
					minute: '2-digit',
					month: 'short',
					day: 'numeric'
				});
			});
			
			// Update datasets with new data
			chartInstance.data.labels = timeLabels;
			chartInstance.data.datasets.forEach((dataset, index) => {
				const slug = slugs[index];
				dataset.data = priceHistory.map(pricePoint => {
					const price = (pricePoint[slug] || 0) * 100;
					return Math.round(price * 10) / 10;
				});
			});
			
			chartInstance.update('none'); // Update without animation for smooth transitions
		}

		// Function to update binary chart with different time ranges
		async function updateBinaryChart(hours) {
			try {
				// Update button states
				document.querySelectorAll('.binary-time-btn').forEach(btn => {
					btn.classList.remove('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white', 'shadow-sm');
					btn.classList.add('text-slate-600', 'dark:text-slate-300');
				});
				
				// Highlight selected button
				const selectedBtn = document.querySelector(`#binaryTimeRangeButtons [data-hours="${hours}"]`);
				if (selectedBtn) {
					selectedBtn.classList.remove('text-slate-600', 'dark:text-slate-300');
					selectedBtn.classList.add('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white', 'shadow-sm');
				}

				// Fetch new data
				const response = await fetch(`/api/markets/${marketId}/historical-prices?hours=${hours}`);
				const historicalData = await response.json();
				
				// Update binary chart
				if (window.binaryChartInstance && historicalData.timestamps && historicalData.prices) {
					updateBinaryChartData(historicalData);
				}
			} catch (error) {
				console.error('Error updating binary chart:', error);
			}
		}

		// Function to update binary chart data
		function updateBinaryChartData(historicalData) {
			const timestamps = historicalData.timestamps || [];
			const priceHistory = historicalData.prices || [];
			
			// Create time labels from actual timestamps
			const timeLabels = timestamps.map(timestamp => {
				const date = new Date(timestamp);
				return date.toLocaleTimeString('en-US', { 
					hour: '2-digit', 
					minute: '2-digit',
					month: 'short',
					day: 'numeric'
				});
			});
			
			// Create datasets for Yes/No
			const yesData = priceHistory.map(pricePoint => {
				const price = (pricePoint.yes || 0.5) * 100;
				return Math.round(price * 10) / 10;
			});
			
			const noData = priceHistory.map(pricePoint => {
				const price = (pricePoint.no || 0.5) * 100;
				return Math.round(price * 10) / 10;
			});
			
			// Update chart data
			window.binaryChartInstance.data.labels = timeLabels;
			window.binaryChartInstance.data.datasets[0].data = yesData; // Yes dataset
			window.binaryChartInstance.data.datasets[1].data = noData; // No dataset
			
			window.binaryChartInstance.update('none'); // Update without animation for smooth transitions
		}

		// Function to handle choice selection from the outcomes table
		function selectChoice(slug, name, probability, price) {
			// Check if buy modal exists (user is authenticated)
			const buyModal = document.querySelector('[x-data]');
			if (buyModal && buyModal._x_dataStack && buyModal._x_dataStack[0]) {
				const modal = buyModal._x_dataStack[0];
				// Update the buy modal state
				modal.selectedChoice = slug;
				modal.updateEstimates();
				
				// Scroll to buy modal on mobile
				if (window.innerWidth < 1024) {
					document.getElementById('sidebar').scrollIntoView({ 
						behavior: 'smooth', 
						block: 'start' 
					});
				}
				
				// Add visual feedback
				highlightChoice(slug);
			}
		}

		// Function to select choice and focus on buy modal (for direct buy button clicks)
		function selectChoiceAndFocus(slug, name, probability, price) {
			selectChoice(slug, name, probability, price);
			
			// Focus on amount input in buy modal
			setTimeout(() => {
				const amountInput = document.querySelector('#buy-modal-container input[type="number"]');
				if (amountInput) {
					amountInput.focus();
				}
			}, 100);
		}

		// Function to highlight selected choice
		function highlightChoice(selectedSlug) {
			// Remove previous highlights
			document.querySelectorAll('.outcome-card').forEach(card => {
				card.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
			});
			
			// Add highlight to selected choice
			const selectedCard = document.querySelector(`[data-choice="${selectedSlug}"]`);
			if (selectedCard) {
				selectedCard.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
			}
		}

		// Binary market selection for yes/no markets
		function selectBinaryChoice(choice) {
			const buyModal = document.querySelector('[x-data]');
			if (buyModal && buyModal._x_dataStack && buyModal._x_dataStack[0]) {
				const modal = buyModal._x_dataStack[0];
				modal.selectedChoice = choice;
				modal.updateEstimates();
				
				if (window.innerWidth < 1024) {
					document.getElementById('sidebar').scrollIntoView({ 
						behavior: 'smooth', 
						block: 'start' 
					});
				}
			}
		}
	</script>
</x-app-layout>