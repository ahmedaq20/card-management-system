<x-filament::page>

    {{-- ✅ شريط التنقل (Breadcrumb) --}}
    <div class="mb-4 flex items-center justify-between">
        <div>
            <nav class="flex text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 rtl:space-x-reverse md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="inline-flex items-center hover:text-primary-600 dark:hover:text-primary-400">
                            القائمة
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2 rtl:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">المستخدمون النشطون</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- ✅ الكارد والجدول --}}
    <x-filament::card>
       {{-- ✅ عنوان الكارد (يدويًا داخل الكارد وليس عبر slot) --}}
    <div class="gap-y-8 py-8 px-6 pt-6 pb-0">
        <h2 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            المستخدمون النشطون
        </h2>
    </div>

        {{-- ✅ محتوى الجدول --}}
        <div class="overflow-x-auto  rounded-xl border border-gray-200 dark:border-gray-700 mt-4">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-sm font-medium text-right text-gray-500">Raids</th>
                        <th class="px-4 py-3 text-sm font-medium text-right text-gray-500">اسم المستخدم</th>
                        <th class="px-4 py-3 text-sm font-medium text-right text-gray-500">ملاحظة</th>
                        <th class="px-4 py-3 text-sm font-medium text-right text-gray-500">IP</th>
                        <th class="px-4 py-3 text-sm font-medium text-right text-gray-500">MAC</th>
                        <th class="px-4 py-3 text-sm font-medium text-right text-gray-500">مدة الاتصال</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-800">
                    @forelse ($activeUsers as $user)
                        <tr>
                            <td class="px-4 py-3 text-sm text-right">
                                @if ($user['radius'] === 'true')
                                    <span  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{-- ✅ check-circle --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="green" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.707 7.293a1 1 0 00-1.414-1.414L11 12.172 8.707 9.879a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l5-5z" clip-rule="evenodd" />
                                        </svg>

                                    </span>
                                @else
                                    <span  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{-- ❌ x-circle --}}
                                        <svg  xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red" fill="red" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zM9.293 9.293a1 1 0 011.414 0L12 10.586l1.293-1.293a1 1 0 111.414 1.414L13.414 12l1.293 1.293a1 1 0 01-1.414 1.414L12 13.414l-1.293 1.293a1 1 0 01-1.414-1.414L10.586 12 9.293 10.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>

                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-200">{{ $user['user'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-200">{{ $user['comment'] ?? ' ' }}</td>

                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-200">{{ $user['address'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-200">{{ $user['mac-address'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-200">{{ $user['uptime'] ?? '-' }}</td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                لا يوجد مستخدمون نشطون حاليًا.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::card>

</x-filament::page>
