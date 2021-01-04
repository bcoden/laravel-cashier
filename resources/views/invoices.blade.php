<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-5">
                    <table class="table-auto border w-full">
                        <thead>
                        <tr class="bg-gray-300">
                            <th class="px-4 py-2 text-left">Issued Date</th>
                            <th class="px-4 py-2 text-left">Total</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                        </thead>
                        @foreach ($invoices as $invoice)
                            <tr class="bg-gray-100">
                                <td class="px-4 py-2 border-gray-300">{{ $invoice->date()->toFormattedDateString() }}</td>
                                <td class="px-4 py-2 border-gray-300">{{ $invoice->total() }}</td>
                                <td class="px-4 py-2 border-gray-300"><a href="/user/invoice/{{ $invoice->id }}" class="text-green-400">Download</a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>